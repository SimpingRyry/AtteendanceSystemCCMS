<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryUnit extends Model
{
    use HasFactory;
        protected $fillable = ['name', 'description'];
        public $timestamps = false;

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
