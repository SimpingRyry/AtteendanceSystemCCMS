<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

     protected $fillable = [
        'id',
        'name',
        'password',
        'is_online',
        'last_seen',
    ];
     protected $casts = [
    'is_online' => 'boolean',
    'last_seen' => 'datetime',
];

}
