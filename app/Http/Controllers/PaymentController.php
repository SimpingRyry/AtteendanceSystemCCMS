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

class PaymentController extends Controller
{
    public function showStatementOfAccount()
{
    $studentId = Auth::user()->student_id;

    $transactionsGrouped = Transaction::where('student_id', $studentId)
        ->orderBy('date')
        ->get()
        ->groupBy('acad_term'); // Group by acad_code (you may use 'acad_term' if you prefer)

    $student = User::where('student_id', $studentId)->first();

    $studentSection = Student::where('id_number', $studentId)->value('section');

    return view('student_payment', [
        'transactionsGrouped' => $transactionsGrouped,
        'student' => $student,
        'studentSection' => $studentSection,
    ]);
}

public function index()
{
    $authOrg = Auth::user()->org;

    $students = User::whereHas('studentList')
        ->with(['studentList', 'transactions' => function ($query) use ($authOrg) {
            $query->where('org', $authOrg); // Only include transactions matching user's org
        }])
        ->get()
        ->unique('student_id');

    // Compute balances
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

    // Get distinct year levels and sections from the related studentList
    $years = Student::select('year')->distinct()->pluck('year')->filter()->sort()->values();
    $sections = Student::select('section')->distinct()->pluck('section')->filter()->sort()->values();

    // Get organization names from OrgList
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
        'amount' => 'required|numeric|min:0.01',
        // remove 'org' from validation
    ]);

    $setting = Setting::where('key', 'academic_term')->first();
    $acadTerm = $setting->value ?? 'Unknown Term';
    $acadCode = $setting->acad_code ?? 'Unknown Code';

    $user = Auth::user();
    $org = $user->org; // Use authenticated user's org
    $processedBy = $user->name . ' - ' . strtoupper($user->role);

    Log::info('Storing payment with data:', [
        'student_id'     => $request->student_id,
        'transaction_type' => 'PAYMENT',
        'org'            => $org,
        'date'           => now()->toDateTimeString(),
        'acad_code'      => $acadCode,
        'acad_term'      => $acadTerm,
        'processed_by'   => $processedBy,
        'fine_amount'    => $request->amount,
    ]);

    Transaction::create([
        'student_id'     => $request->student_id,
        'transaction_type' => 'PAYMENT',
        'org'            => $org,
        'date'           => now(),
        'acad_code'      => $acadCode,
        'acad_term'      => $acadTerm,
        'processed_by'   => $processedBy,
        'fine_amount'    => $request->amount,
    ]);

    return back()->with('success', 'Payment recorded successfully.');
}

}
