<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'event_id',
        'date',
        'time_in1',
        'time_out1',
        'time_in2',
        'time_out2',
        'status',
        'is_answered',
        'morning_status',
        'afternoon_status',
        'excuse_reason',
        'excuse_letter',
    ];
    protected $casts = [
    'is_answered' => 'boolean',
];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id_number');
    }
    public function event()
{
    return $this->belongsTo(Event::class);
}
}
