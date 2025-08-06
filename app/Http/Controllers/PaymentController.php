<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OrgList;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Logs;

class PaymentController extends Controller
{
public function showStatementOfAccount(Request $request)
{
    $studentId = Auth::user()->student_id;
    $selectedOrg = $request->input('organization'); // Get selected org from dropdown

    $query = Transaction::where('student_id', $studentId);

    // Only filter by org if it's NOT "All" or null
    if ($selectedOrg && $selectedOrg !== 'All') {
        $query->where('org', $selectedOrg);
    }

    $transactionsGrouped = $query->orderBy('date')->get()->groupBy('acad_term');

    $student = User::where('student_id', $studentId)->first();
    $studentSection = Student::where('id_number', $studentId)->value('section');

    return view('student_payment', [
        'transactionsGrouped' => $transactionsGrouped,
        'student' => $student,
        'studentSection' => $studentSection,
        'selectedOrg' => $selectedOrg,
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

    // Filters
    $years = Student::select('year')->distinct()->pluck('year')->filter()->sort()->values();
    $sections = Student::select('section')->distinct()->pluck('section')->filter()->sort()->values();
    $orgs = OrgList::select('org_name')->distinct()->pluck('org_name');

    return view('payment_page2', compact('students', 'balances', 'years', 'sections', 'orgs'));
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
        'student_id' => 'required|string',
        'amount'     => 'required|numeric|min:0.01',
        'or_number'  => 'required|string|max:255', // ✅ Add validation for OR number
    ]);

    $setting = Setting::where('key', 'academic_term')->first();
    $acadTerm = $setting->value ?? 'Unknown Term';
    $acadCode = $setting->acad_code ?? 'Unknown Code';

    $user = Auth::user();
    $org = $user->org;
    $processedBy = $user->name . ' - ' . strtoupper($user->role);

    Log::info('Storing payment with data:', [
        'student_id'     => $request->student_id,
        'transaction_type' => 'PAYMENT',
        'org'            => $org,
        'or_num'         => $request->or_number, // ✅ Log OR Number
        'date'           => now('Asia/Manila')->toDateTimeString(),
        'acad_code'      => $acadCode,
        'acad_term'      => $acadTerm,
        'processed_by'   => $processedBy,
        'fine_amount'    => $request->amount,
    ]);

    Transaction::create([
        'student_id'     => $request->student_id,
        'transaction_type' => 'PAYMENT',
        'org'            => $org,
        'or_num'         => $request->or_number, // ✅ Save OR Number
        'date'           => now('Asia/Manila'),
        'acad_code'      => $acadCode,
        'acad_term'      => $acadTerm,
        'processed_by'   => $processedBy,
        'fine_amount'    => $request->amount,
    ]);

    Logs::create([
    'action' => 'Create',
    'description' => 'Recorded payment for student ID ' . $request->student_id . ' with OR No. ' . $request->or_number . ' (₱' . number_format($request->amount, 2) . ')',
    'user' => auth()->user()->name ?? 'System',
    'date_time' => now('Asia/Manila'),
    'type' => 'Payment',
]);

    return back()->with('success', 'Payment recorded successfully.');
}

}
