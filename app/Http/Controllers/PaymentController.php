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
}
