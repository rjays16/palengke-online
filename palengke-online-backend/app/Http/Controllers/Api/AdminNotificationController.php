<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::orderBy('created_at', 'desc')->get()->map(fn ($n) => [
            'id' => $n->id,
            'type' => $n->type,
            'message' => $n->message,
            'link' => $n->link,
            'time' => $n->created_at->diffForHumans(),
            'read' => $n->is_read,
            'data' => $n->data,
        ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Notification::where('is_read', false)->count(),
        ]);
    }

    public function markRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Notification marked as read.']);
    }

    public function markAllRead()
    {
        Notification::where('is_read', false)->update(['is_read' => true]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();

        return response()->json(['message' => 'Notification deleted.']);
    }

    public function unreadCount()
    {
        return response()->json([
            'unread_count' => Notification::where('is_read', false)->count(),
        ]);
    }
}
