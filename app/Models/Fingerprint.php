<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fingerprint extends Model
{
    use HasFactory;
    protected $table = 'fingerprints';
    public $timestamps = false;

        protected $fillable = ['user_id', 'template', 'image_url'];


}
