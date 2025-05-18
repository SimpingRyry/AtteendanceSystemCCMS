<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $table = 'evaluation';
    protected $fillable = ['title', 'description'];

    public function questions()
    {
        return $this->hasMany(EvaluationQuestion::class)->orderBy('order');
    }
    
}

