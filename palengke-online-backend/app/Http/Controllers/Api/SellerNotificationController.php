<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;

class SellerNotificationController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $notifications = Notification::where('seller_registration_id', $seller->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'time' => $n->created_at->toIso8601String(),
                'read' => $n->is_read,
                'orderId' => $n->data['order_id'] ?? null,
                'customerId' => $n->data['customer_id'] ?? null,
                'productId' => $n->data['product_id'] ?? null,
            ]);

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    public function markRead(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Notification marked as read.']);
    }

    public function markAllRead(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        Notification::where('seller_registration_id', $seller->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();

        return response()->json(['message' => 'Notification deleted.']);
    }

    public function clearAll(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        Notification::where('seller_registration_id', $seller->id)->delete();

        return response()->json(['message' => 'All notifications cleared.']);
    }

}
