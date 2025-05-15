<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = ['title', 'description'];

    public function questions()
    {
        return $this->hasMany(EvaluationQuestion::class);
    }
}

