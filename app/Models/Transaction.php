<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transaction';
    public $timestamps = false;

    protected $fillable = [
        'student_id', 'event', 'transaction_type', 'fine_amount', 'org', 'date','acad_term','acad_code','processed_by'
    ];
}
