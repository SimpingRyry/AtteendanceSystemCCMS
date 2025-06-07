<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FineSetting extends Model
{
    use HasFactory;
    protected $table = 'fine_settings';

  

    protected $fillable = [
        'id',
        'absent_member',
        'late,member',
        'absent_officer',
        'late_officer',
        'grace_period_minutes',
        'org'

    ];
}
