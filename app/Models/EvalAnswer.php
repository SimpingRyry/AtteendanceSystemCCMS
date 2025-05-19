<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvalAnswer extends Model
{
    protected $fillable = [
        'evaluation_id',
        'evaluation_question_id',
        'student_id',
        'answer',
    ];

    public function evaluation() { return $this->belongsTo(Evaluation::class); }
    public function question()   { return $this->belongsTo(EvaluationQuestion::class,'question_id'); }
    public function student()    { return $this->belongsTo(User::class,'student_id'); }
}
