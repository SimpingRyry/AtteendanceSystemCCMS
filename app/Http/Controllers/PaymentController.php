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
    $selectedOrg = $request->input('organization');
    $semester = $request->input('semester'); // new semester input

    $query = Transaction::where('student_id', $studentId);

    // Organization filter
    if ($selectedOrg && $selectedOrg !== 'All') {
        $query->where('org', $selectedOrg);
    }

    // Semester filter (match acad_code only)
    if ($semester) {
        $query->whereRaw("LEFT(acad_term, LOCATE(' ', acad_term) - 1) = ?", [$semester]);
    }

    $transactionsGrouped = $query->orderBy('date')->get()
        ->groupBy(function ($transaction) {
            // Group by the acad_code only (before first space)
            return strtok($transaction->acad_term, ' ');
        });

    $student = User::where('student_id', $studentId)->first();
    $studentSection = Student::where('id_number', $studentId)->value('section');

    // Get user's org record
    $userOrg = OrgList::where('org_name', Auth::user()->org)->first();
    $orgOptions = collect();

    if ($userOrg) {
        if ($userOrg->parent) {
            $orgOptions->push($userOrg->parent->org_name);
        }
        $orgOptions->push($userOrg->org_name);
        if ($userOrg->children->isNotEmpty()) {
            foreach ($userOrg->children as $child) {
                $orgOptions->push($child->org_name);
            }
        }
    }

    return view('student_payment', [
        'transactionsGrouped' => $transactionsGrouped,
        'student' => $student,
        'studentSection' => $studentSection,
        'selectedOrg' => $selectedOrg,
        'orgOptions' => $orgOptions,
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
        'student_id'    => 'required|string',
        'event_id'      => 'required|integer|exists:events,id',
        'amount'        => 'required|numeric|min:0.01',
        'or_number'     => 'required|string|max:255',
        'payment_date'  => 'required|date',
    ]);

    // Get academic info
    $setting = Setting::where('key', 'academic_term')->first();
    $acadTerm = $setting->value ?? 'Unknown Term';
    $acadCode = $setting->acad_code ?? 'Unknown Code';

    // Officer/org
    $user = Auth::user();
    $org = $user->org;
    $processedBy = $user->name . ' - ' . strtoupper($user->role);

    // ✅ Fetch event details
    $event = Event::find($request->event_id);

    // ✅ Create payment transaction
    Transaction::create([
        'student_id'       => $request->student_id,
        'event_id'         => $request->event_id,
        'event'            => $event->name,
        'transaction_type' => 'PAYMENT',
        'org'              => $org,
        'or_num'           => $request->or_number,
        'date'             => $request->payment_date,
        'acad_code'        => $acadCode,
        'acad_term'        => $acadTerm,
        'processed_by'     => $processedBy,
        'fine_amount'      => $request->amount,
        'status'           => 'Paid', // optional if you want this here too
    ]);

    // ✅ Mark the original fine as Paid
    Transaction::where('student_id', $request->student_id)
        ->where('event_id', $request->event_id)
        ->where('transaction_type', 'FINE')
        ->where('status', 'Unpaid')
        ->update(['status' => 'Paid']);

    // ✅ Log the action
    Logs::create([
        'action'      => 'Create',
        'description' => 'Recorded payment for student ID ' . $request->student_id .
                         ' for event "' . $event->name . '"' .
                         ' with OR No. ' . $request->or_number .
                         ' (₱' . number_format($request->amount, 2) . ')',
        'user'        => $user->name ?? 'System',
        'date_time'   => now('Asia/Manila'),
        'type'        => 'Payment',
    ]);

    return back()->with('success', 'Payment for event "' . $event->name . '" recorded successfully and fine marked as Paid.');
}

public function gcashSuccess(Request $request)
{
    $sourceId = $request->input('source.id') 
        ?? ($request->query('source')['id'] ?? null)
        ?? session('gcash_source_id');

    $organization = $request->query('organization')
        ?? session('gcash_organization');

    if (!$sourceId) {
        return redirect()->route('student_payment')->with('error', 'Missing payment source ID.');
    }

    Log::info('GCash payment success', [
        'source_id' => $sourceId,
        'organization' => $organization,
    ]);

    // Verify source status
    $sourceResponse = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
        ->get("https://api.paymongo.com/v1/sources/{$sourceId}");

    $sourceData = $sourceResponse->json('data.attributes');
    Log::info('Source data received', $sourceData);
    // Wait until source is 'chargeable'
    if ($sourceData['status'] === 'chargeable') {

        // Create payment
        $paymentResponse = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
            ->post('https://api.paymongo.com/v1/payments', [
                'data' => [
                    'attributes' => [
                        'amount' => $sourceData['amount'],
                        'source' => [
                            'id' => $sourceId,
                            'type' => 'source'
                        ],
                        'currency' => 'PHP'
                    ]
                ]
            ]);

        $paymentData = $paymentResponse->json('data');

        // Store payment ID in session for reference
        session(['gcash_payment_id' => $paymentData['id'] ?? null]);

        $gcashRef = $paymentData['attributes']['reference_number'] ?? strtoupper(uniqid());
        

        $user = auth()->user();

        Transaction::create([
            'student_id'       => $user->student_id,
            'transaction_type' => 'PAYMENT',
            'org'              => $organization ?? $user->org,
            'or_num'           => 'GCASH-' . $gcashRef,
            'date'             => now('Asia/Manila'),
            'acad_code'        => Setting::where('key', 'academic_term')->value('acad_code'),
            'acad_term'        => Setting::where('key', 'academic_term')->value('value'),
            'processed_by'     => 'GCASH',
            'fine_amount'      => $sourceData['amount'] / 100,
        ]);

        Logs::create([
            'action'      => 'Create',
            'description' => 'Recorded GCash payment for student ID ' . $user->student_id .
                             ' (₱' . number_format($sourceData['amount'] / 100, 2) . ')',
            'user'        => $user->name,
            'date_time'   => now('Asia/Manila'),
            'type'        => 'Payment',
        ]);

        return redirect()->route('student_payment')->with('success', 'GCash payment recorded successfully.');
    }

    return redirect()->route('student_payment')->with('error', 'Payment not yet completed. Please wait a moment and try again.');
}

}
