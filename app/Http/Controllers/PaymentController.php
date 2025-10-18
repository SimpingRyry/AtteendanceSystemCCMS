<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\User;
use App\Models\Event;
use App\Models\OrgList;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
public function showStatementOfAccount(Request $request)
{
    $studentId = Auth::user()->student_id;
    $semester = $request->input('semester'); // still keep semester filter

    $query = Transaction::where('student_id', $studentId);

    // ✅ Only keep semester filter
    if ($semester) {
        $query->whereRaw("LEFT(acad_term, LOCATE(' ', acad_term) - 1) = ?", [$semester]);
    }

    $transactionsGrouped = $query->orderBy('date')->get()
        ->groupBy(function ($transaction) {
            return strtok($transaction->acad_term, ' ');
        });

    $student = User::where('student_id', $studentId)->first();
    $studentSection = Student::where('id_number', $studentId)->value('section');

    return view('student_payment', [
        'transactionsGrouped' => $transactionsGrouped,
        'student' => $student,
        'studentSection' => $studentSection,
        'semester' => $semester,
    ]);
}



public function index()
{
    $authUser = Auth::user();
    $authOrg = $authUser->org;
    $userRole = $authUser->role;

    // Get the authenticated org record with its children
    $org = OrgList::where('org_name', $authOrg)->with('children')->first();
    $childOrgNames = $org?->children->pluck('org_name')->toArray() ?? [];
    $isParentOrg = !empty($childOrgNames);

    $studentsQuery = User::query();

    if ($userRole === 'Super Admin') {
        // Show all users that have a student list entry
        $studentsQuery->whereHas('studentList');
    } elseif ($isParentOrg) {
        // For parent org: fetch members from all child orgs
        $studentsQuery->whereIn('org', $childOrgNames)
                      ->where('role', 'Member');
    } else {
        // For child org: fetch all members under that org regardless of transactions
        $studentsQuery->where('org', $authOrg)
                      ->where('role', 'Member');
    }

    // Always load only the transactions related to the logged-in org (parent or child)
    $students = $studentsQuery
        ->with([
            'studentList',
            'transactions' => function ($query) use ($authOrg) {
                $query->where('org', $authOrg);
            }
        ])
        ->get()
        ->unique('student_id');

    // Compute balances based on filtered transactions
    $balances = [];
    foreach ($students as $student) {
        $balance = 0;
        foreach ($student->transactions as $transaction) {
            if ($transaction->transaction_type === 'FINE') {
                $balance += $transaction->fine_amount;
            } elseif ($transaction->transaction_type === 'PAYMENT') {
                $balance -= $transaction->fine_amount;
            }
        }
        $balances[$student->student_id] = $balance;
    }

    // Dropdown list: unpaid fine events for this logged-in student


    // Filters
    $years = Student::select('year')->distinct()->pluck('year')->filter()->sort()->values();
    $sections = Student::select('section')->distinct()->pluck('section')->filter()->sort()->values();
    $orgs = OrgList::select('org_name')->distinct()->pluck('org_name');

    return view('payment_page2', compact('students', 'balances', 'years', 'sections', 'orgs', ));
}


public function getUnpaidEvents($studentId)
{
    $authOrg = Auth::user()->org;

    $unpaidEvents = DB::table('transaction')
        ->join('events', 'transaction.event_id', '=', 'events.id')
        ->where('transaction.student_id', $studentId)
        ->where('transaction.status', 'Unpaid')
        ->where('transaction.org', $authOrg)
        ->select('events.id', 'events.name', 'events.event_date', 'transaction.fine_amount')
        ->distinct()
        ->get();

    return response()->json($unpaidEvents);
}
public function getStudentUnpaidEvents($studentId)
{
    // Fetch all unpaid fines for this student (no org restriction for online)
    $unpaidEvents = DB::table('transaction')
        ->join('events', 'transaction.event_id', '=', 'events.id')
        ->where('transaction.student_id', $studentId)
        ->where('transaction.status', 'Unpaid')
        ->where('transaction.transaction_type', 'FINE')
        ->select(
            'events.id',
            'events.name',
            'events.event_date',
            'transaction.org', // ✅ Include organization
            'transaction.fine_amount'
        )
        ->distinct()
        ->get();

    return response()->json($unpaidEvents);
}
public function loadStudentSOA($studentId)
{
    $student = User::where('student_id', $studentId)->first();

    if (!$student) {
        return "<p class='text-danger text-center'>Student not found.</p>";
    }

    $studentSection = Student::where('id_number', $studentId)->value('section');

    $authOrg = Auth::user()->org;

    $transactionsGrouped = Transaction::where('student_id', $studentId)
        ->where('org', $authOrg) // Added condition here
        ->orderBy('date')
        ->get()
        ->groupBy('acad_code');

    return view('layout.student_soa_modal_content', [
        'student' => $student,
        'transactionsGrouped' => $transactionsGrouped,
        'studentSection' => $studentSection,
    ]);
}
public function storePayment(Request $request)
{
    Log::info('storePayment request received:', $request->all());

    $request->validate([
        'student_id'       => 'required|string',
        'selected_events'  => 'required|json', // JSON array from hidden input
        'amount'           => 'required|numeric|min:0.01',
        'or_number'        => 'required|string|max:255',
        'payment_date'     => 'required|date',
    ]);

    // Decode selected events (array of IDs)
    $eventIds = json_decode($request->selected_events, true);

    if (empty($eventIds) || !is_array($eventIds)) {
        return back()->withErrors(['selected_events' => 'No events selected for payment.']);
    }

    // Academic term info
    $setting = Setting::where('key', 'academic_term')->first();
    $acadTerm = $setting->value ?? 'Unknown Term';
    $acadCode = $setting->acad_code ?? 'Unknown Code';

    // Officer/org info
    $user = Auth::user();
    $org = $user->org;
    $processedBy = $user->name . ' - ' . strtoupper($user->role);

    $studentId = $request->student_id;
    $totalAmount = 0;
    $paidEvents = [];

    foreach ($eventIds as $eventId) {
        $event = Event::find($eventId);
        if (!$event) continue;

        // ✅ Now find the fine using BOTH event_id and event_date
        $fine = Transaction::where('student_id', $studentId)
            ->where('event_id', $eventId)
            ->whereDate('date', $event->event_date)
            ->where('transaction_type', 'FINE')
            ->where('status', 'Unpaid')
            ->first();

        if (!$fine) continue;

        $amount = $fine->fine_amount;
        $totalAmount += $amount;
        $paidEvents[] = $event->name . ' (' . $event->event_date . ')';

        // ✅ Create payment transaction record
        Transaction::create([
            'student_id'       => $studentId,
            'event_id'         => $eventId,
            'event'            => $event->name,
            'transaction_type' => 'PAYMENT',
            'org'              => $org,
            'or_num'           => $request->or_number,
            'date'             => $request->payment_date,
            'acad_code'        => $acadCode,
            'acad_term'        => $acadTerm,
            'processed_by'     => $processedBy,
            'fine_amount'      => $amount,
            'status'           => 'Paid',
        ]);

        // ✅ Update fine status to Paid using event_id + event_date
        Transaction::where('student_id', $studentId)
            ->where('event_id', $eventId)
            ->whereDate('date', $event->event_date)
            ->where('transaction_type', 'FINE')
            ->where('status', 'Unpaid')
            ->update(['status' => 'Paid']);
    }

    // ✅ Log the entire payment operation
    if (!empty($paidEvents)) {
        Logs::create([
            'action'      => 'Create',
            'description' => 'Recorded payment for student ID ' . $studentId .
                             ' for events: ' . implode(', ', $paidEvents) .
                             ' with OR No. ' . $request->or_number .
                             ' (₱' . number_format($totalAmount, 2) . ')',
            'user'        => $user->name ?? 'System',
            'date_time'   => now('Asia/Manila'),
            'type'        => 'Payment',
        ]);
    }

    return back()->with('success',
        'Payment recorded successfully for ' . count($paidEvents) .
        ' event(s). Total: ₱' . number_format($totalAmount, 2)
    );
}


public function gcashSuccess(Request $request)
{
    $sourceId = session('gcash_source_id');
    $selectedEvents = session('gcash_selected_events', []);
    $amount = session('gcash_amount', 0);

    if (!$sourceId || empty($selectedEvents)) {
        return redirect()->route('student_payment')->with('error', 'Missing payment details.');
    }

    Log::info('GCash payment success callback', [
        'source_id' => $sourceId,
        'selected_events' => $selectedEvents,
    ]);

    // ✅ Verify GCash source status
    $sourceResponse = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
        ->get("https://api.paymongo.com/v1/sources/{$sourceId}");

    $sourceData = $sourceResponse->json('data.attributes');

    if (!isset($sourceData['status']) || $sourceData['status'] !== 'chargeable') {
        return redirect()->route('student_payment')
            ->with('error', 'Payment not yet completed. Please wait a moment and try again.');
    }

    // ✅ Create payment in PayMongo
    $paymentResponse = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
        ->post('https://api.paymongo.com/v1/payments', [
            'data' => [
                'attributes' => [
                    'amount' => $sourceData['amount'],
                    'source' => [
                        'id' => $sourceId,
                        'type' => 'source',
                    ],
                    'currency' => 'PHP',
                ],
            ],
        ]);

    $paymentData = $paymentResponse->json('data');
    $gcashRef = $paymentData['attributes']['reference_number'] ?? strtoupper(uniqid('GCASH-'));

    // ✅ Get student and academic details
    $user = auth()->user();
    $studentId = $user->student_id;
    $setting = Setting::where('key', 'academic_term')->first();
    $acadTerm = $setting->value ?? 'Unknown Term';
    $acadCode = $setting->acad_code ?? 'Unknown Code';

    $totalPaid = 0;
    $paidEvents = [];

    // ✅ Use same logic as storePayment: event_id + event_date
    foreach ($selectedEvents as $eventId) {
        $event = Event::find($eventId);
        if (!$event) continue;

        // Find unpaid fine using BOTH event_id and event_date
        $fine = Transaction::where('student_id', $studentId)
            ->where('event_id', $eventId)
            ->whereDate('date', $event->event_date)
            ->where('transaction_type', 'FINE')
            ->where('status', 'Unpaid')
            ->first();

        if (!$fine) continue;

        $org = $fine->org ?? 'Unknown Org';
        $amountPaid = $fine->fine_amount;
        $totalPaid += $amountPaid;
        $paidEvents[] = $event->name . ' (' . $event->event_date . ')';

        // ✅ Create PAYMENT transaction
        Transaction::create([
            'student_id'       => $studentId,
            'event_id'         => $event->id,
            'event'            => $event->name,
            'transaction_type' => 'PAYMENT',
            'org'              => $org,
            'or_num'           => $gcashRef,
            'date'             => now('Asia/Manila'),
            'acad_code'        => $acadCode,
            'acad_term'        => $acadTerm,
            'processed_by'     => 'GCASH',
            'fine_amount'      => $amountPaid,
            'status'           => 'Paid',
        ]);

        // ✅ Update fine record to Paid (event_id + event_date)
        Transaction::where('student_id', $studentId)
            ->where('event_id', $eventId)
            ->whereDate('date', $event->event_date)
            ->where('transaction_type', 'FINE')
            ->where('status', 'Unpaid')
            ->update(['status' => 'Paid']);
    }

    // ✅ Log the payment
    if (!empty($paidEvents)) {
        Logs::create([
            'action'      => 'Create',
            'description' => 'Recorded GCash payment for student ID ' . $studentId .
                             ' for events: ' . implode(', ', $paidEvents) .
                             ' (₱' . number_format($totalPaid, 2) . ')',
            'user'        => $user->name ?? 'System',
            'date_time'   => now('Asia/Manila'),
            'type'        => 'Payment',
        ]);
    }

    // ✅ Clean up session
    session()->forget(['gcash_source_id', 'gcash_selected_events', 'gcash_amount']);

    return redirect()->route('student_payment')
        ->with('success', 'GCash payment recorded successfully for ' . count($paidEvents) . ' event(s).');
}

}
