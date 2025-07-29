<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficerRole extends Model
{
    use HasFactory;

    protected $table = 'officer_roles';
    public $timestamps = false;

    protected $fillable = [
        'org',
        'title',
        'description'

];
}
