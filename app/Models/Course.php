<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

      protected $fillable = ['delivery_unit_id', 'name', 'code'];
      public $timestamps = false;

    public function deliveryUnit()
    {
        return $this->belongsTo(DeliveryUnit::class);
    }
}
