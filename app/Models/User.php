<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\MustVerifyEmail;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens, Notifiable, MustVerifyEmail;
    

    protected $table = 'users';

    protected $primaryKey = 'id'; // ✅ matches your DB

    public $timestamps = false; // ✅ no timestamps in your schema

    protected $fillable = [
        'name', 'email', 'password', 'role','student_id' ,'picture','org','email_verified_at' ,'term'
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


public function studentList()
{
    return $this->hasOne(Student::class, 'id_number', 'student_id');
}

public function transactions()
{
    return $this->hasMany(Transaction::class, 'student_id', 'student_id');
}

public function getBalanceAttribute()
{
    return $this->transactions->reduce(function ($carry, $transaction) {
        return $transaction->transaction_type === 'FINE'
            ? $carry + $transaction->fine_amount
            : ($transaction->type === 'PAYMENT' ? $carry - $transaction->fine_amount : $carry);
    }, 0);
}

public function scopeActiveOfficers($query)
{
    $currentTerm = Setting::where('key', 'academic_term')->value('value');
    return $query->where('role', 'like', '%- Officer%')->where('term', $currentTerm);
}
}