<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
// app/Models/Notification.php

protected $fillable = ['title', 'message', 'user_id', 'seen'];

}
