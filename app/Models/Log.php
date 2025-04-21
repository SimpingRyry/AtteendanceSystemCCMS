<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs'; // optional if your table name is `logs`

    protected $fillable = [
        'action',
        'description',
        'user',
        'date_time'
    ];

    public $timestamps = false; // set to true if your table has `created_at` and `updated_at` columns
}
