<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationAssignment extends Model
{
    use HasFactory;
    protected $table = 'eval_assignment';

    protected $fillable = [
        'id', 'evaluation_id', 'event_id', 'created_at','updated_at'
    ];
    public function evaluation()
{
    return $this->belongsTo(Evaluation::class);
}

public function event()
{
    return $this->belongsTo(Event::class);
}
}
