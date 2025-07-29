<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value','acad_code','org','morning_in','morning_out','afternoon_in','afternoon_out'];

    public $timestamps = true;
}
