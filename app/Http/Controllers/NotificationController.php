<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Get unread count
    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
            
        return response()->json(['count' => $count]);
    }

    // Get all notifications
    public function index()
  
{
    $notifications = Notification::where('user_id', Auth::id())
        ->latest()
        ->paginate(10); // Keep it as paginator of models

    return view('partials.notification-index', compact('notifications'));
}
    // Mark as read
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
            
        $notification->update(['read_at' => now()]);

        
        return response()->json(['success' => true]);
    }

    // Mark all as read
   public function markAllAsRead()
{
    Notification::where('user_id', Auth::id())
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

    return response()->json(['success' => true]);
}


    // Clear all notifications
    public function clearAll()
    {
        Notification::where('user_id', Auth::id())->delete();
        
        return response()->json(['success' => true]);
    }

}