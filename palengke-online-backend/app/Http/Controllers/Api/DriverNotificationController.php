<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class DriverNotificationController extends Controller
{
    public function index(Request $request)
    {
        $driver = $request->user();

        $notifications = Notification::where('driver_registration_id', $driver->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'category' => $n->category ?? 'all',
                'time' => $n->created_at->diffForHumans(),
                'read' => $n->is_read,
                'data' => $n->data,
            ]);

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    public function unreadCount(Request $request)
    {
        $driver = $request->user();

        $count = Notification::where('driver_registration_id', $driver->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    public function markRead(Request $request, $id)
    {
        $driver = $request->user();

        $notification = Notification::where('driver_registration_id', $driver->id)
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found.'], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Notification marked as read.']);
    }

    public function markAllRead(Request $request)
    {
        $driver = $request->user();

        Notification::where('driver_registration_id', $driver->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    public function destroy(Request $request, $id)
    {
        $driver = $request->user();

        $notification = Notification::where('driver_registration_id', $driver->id)
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found.'], 404);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted.']);
    }

    public function clearAll(Request $request)
    {
        $driver = $request->user();

        Notification::where('driver_registration_id', $driver->id)->delete();

        return response()->json(['message' => 'All notifications cleared.']);
    }
}
