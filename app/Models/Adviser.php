<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adviser extends Model
{
    use HasFactory;

    // Explicitly define table name if it's not plural
    protected $table = 'adviser';
    public $timestamps = false;


    protected $fillable = [
        'name',
        'email',
        'org',
        'password'
    ];
}
