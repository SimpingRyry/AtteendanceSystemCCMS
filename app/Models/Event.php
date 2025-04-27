<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Disable automatic timestamps
    public $timestamps = false;

    // Define the table name if it's not the plural form of the model name
    protected $table = 'events';

    // Define the fields that can be mass-assigned
    protected $fillable = [
        'name',
        'venue',
        'event_date',
        'timeouts',
        'course',
        'times', // Store times as JSON
    ];

    // If you're storing the times as a JSON column, you can cast it
    protected $casts = [
        'times' => 'array',
    ];
}
