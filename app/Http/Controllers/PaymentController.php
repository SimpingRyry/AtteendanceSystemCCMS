<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    $students = User::whereHas('studentList') // only users that have a studentList
        ->with(['studentList', 'transactions'])
        ->get()
        ->unique('student_id'); // Filter duplicates by student_id

    return view('payment_page2', compact('students'));
}

public function loadStudentSOA($studentId)
{
    $student = User::where('student_id', $studentId)->first();

    if (!$student) {
        return "<p class='text-danger text-center'>Student not found.</p>";
    }
    $studentSection = Student::where('id_number', $studentId)->value('section');

    $transactionsGrouped = Transaction::where('student_id', $studentId)
        ->orderBy('date')
        ->get()
        ->groupBy('acad_code');

    

    return view('layout.student_soa_modal_content', [
        'student' => $student,
        'transactionsGrouped' => $transactionsGrouped,
        'studentSection' => $studentSection,
        
    ]);
}
}
