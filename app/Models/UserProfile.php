<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profiles'; // your table name

    protected $fillable = [
        'name',
        'email',
        'program',
        'position',
        'mobile',
        'address',
        'photo'
    ];

    public $timestamps = false; // if your table doesn't have created_at/updated_at
}
