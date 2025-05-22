<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $table = 'evaluation';
    protected $fillable = ['title', 'description','event','course'];

    public function questions()
    {
        return $this->hasMany(EvaluationQuestion::class, 'evaluation_id');
    }
    public function event()
{
    return $this->belongsTo(Event::class);
}
    
}

