<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id'; // ✅ matches your DB

    public $timestamps = false; // ✅ no timestamps in your schema

    protected $fillable = [
        'name', 'email', 'password', 'role', 'picture','org'
    ];

    protected $hidden = [
        'password',
    ];

    // ✅ Accessor to let Laravel treat `username` as `name`
    public function getNameAttribute()
    {
        return $this->attributes['name'];
    }

    // ✅ Accessor for image field
    public function getImageAttribute()
    {
        return $this->attributes['picture'];
    }

    public function organization()
{
    return $this->belongsTo(OrgList::class, 'org', 'org_name');
}
}