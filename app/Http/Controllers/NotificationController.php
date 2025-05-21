<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        return response()->json($notifications);
    }
public function markAsRead($id)
{
    $notification = Notification::findOrFail($id);
    $notification->read_at = now();
    $notification->save();

    return response()->json(['success' => true]);
}

public function markAllAsRead()
{
    Notification::where('user_id', auth()->id())
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

    return response()->json(['success' => true]);
}

}
