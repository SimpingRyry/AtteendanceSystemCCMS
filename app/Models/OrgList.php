<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrgList extends Model
{
    use HasFactory;

    // explicitly define the table name
    protected $table = 'org_list';

    // optionally allow mass assignment (especially useful for admin panel uploads)
    protected $fillable = [
        'org_name',
        'description',
        'org_logo',
        'bg_image',
    ];
}
