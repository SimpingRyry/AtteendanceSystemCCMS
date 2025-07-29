<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FineHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'updated_by',
        'changed_at',
        'org',
        'acad_code'
    ];
}
