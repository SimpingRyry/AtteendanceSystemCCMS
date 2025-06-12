<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationAnswer extends Model
{
    use HasFactory;
    protected $table = 'evaluation_answers';

    protected $fillable = [
        'evaluation_id',
        'question_id',
        'student_id',
        'answer',
        'event_id'
    ];

    // Relationships (optional but useful)
    
    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }
    public function events()
    {
        return $this->belongsTo(Event::class);
    }
    public function question()
    {
        return $this->belongsTo(EvaluationQuestion::class, 'question_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id'); // change if you have a separate Student model
    }
}
