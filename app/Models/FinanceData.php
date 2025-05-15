<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceData extends Model
{
    protected $table = 'finance_data';

    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'org',
        'program',
        'amount',
        'date_issued'
    ];
}
