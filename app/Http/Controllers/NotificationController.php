<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orWhereNull('user_id') // Broadcast notifications
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications count (AJAX)
     */
    public function unreadCount()
    {
        $count = Notification::where(function($query) {
            $query->where('user_id', Auth::id())
                  ->orWhereNull('user_id');
        })
        ->where('is_read', false)
        ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (AJAX)
     */
    public function recent()
    {
        $notifications = Notification::where(function($query) {
            $query->where('user_id', Auth::id())
                  ->orWhereNull('user_id');
        })
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

        return response()->json($notifications);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if user has access to this notification
        if ($notification->user_id && $notification->user_id != Auth::id()) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            Toastr::error('Unauthorized', 'Error');
            return redirect()->back();
        }

        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        Toastr::success('Notification marked as read', 'Success');
        return redirect()->back();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
        }

        Toastr::success('All notifications marked as read', 'Success');
        return redirect()->back();
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id && $notification->user_id != Auth::id()) {
            Toastr::error('Unauthorized', 'Error');
            return redirect()->back();
        }

        $notification->delete();
        Toastr::success('Notification deleted', 'Success');
        return redirect()->back();
    }

    /**
     * Create a notification (helper method for other controllers)
     */
    public static function create($data)
    {
        return Notification::createNotification($data);
    }
}
