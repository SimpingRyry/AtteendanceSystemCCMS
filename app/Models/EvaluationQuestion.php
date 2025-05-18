<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationQuestion extends Model
{
    protected $table = 'evaluation_questions';
    protected $casts = [
    'options' => 'array',
];
    protected $fillable = [
        'evaluation_id', 'question', 'type', 'order','options', 'is_required'
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }
}

