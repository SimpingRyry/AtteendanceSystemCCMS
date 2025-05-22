<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id', 'question_id', 'student_id', 'answer'
    ];
}
