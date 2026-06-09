<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $orders = Order::where('seller_registration_id', $seller->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'delivery_address' => $order->delivery_address,
                    'latitude' => $order->latitude ? (float) $order->latitude : null,
                    'longitude' => $order->longitude ? (float) $order->longitude : null,
                    'items' => $order->items,
                    'item_count' => count($order->items ?? []),
                    'total' => (float) $order->total,
                    'status' => $order->status,
                    'payment_method' => $order->payment_method,
                    'created_at' => $order->created_at,
                    'date' => $order->created_at ? $order->created_at->format('Y-m-d') : null,
                    'time' => $order->created_at ? $order->created_at->diffForHumans() : null,
                ];
            });

        $today = now()->startOfDay();
        $todayOrders = $orders->filter(fn($o) => $o['created_at'] && $o['created_at'] >= $today);
        $weekStart = now()->startOfWeek();

        return response()->json([
            'orders' => $orders,
            'stats' => [
                'today_orders' => $todayOrders->count(),
                'today_sales' => $todayOrders->sum('total'),
                'total_products' => $orders->sum('item_count'),
                'week_sales' => $orders->filter(fn($o) => $o['created_at'] && $o['created_at'] >= $weekStart)->sum('total'),
            ],
        ]);
    }

    public function show(Request $request, $id)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $order = Order::where('seller_registration_id', $seller->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        return response()->json([
            'order' => [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'delivery_address' => $order->delivery_address,
                'latitude' => $order->latitude ? (float) $order->latitude : null,
                'longitude' => $order->longitude ? (float) $order->longitude : null,
                'items' => $order->items,
                'item_count' => count($order->items ?? []),
                'total' => (float) $order->total,
                'status' => $order->status,
                'payment_method' => $order->payment_method,
                'created_at' => $order->created_at,
            ],
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'status' => 'required|string|in:pending,processing,completed,cancelled',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $order = Order::where('seller_registration_id', $seller->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
            ],
        ]);
    }
}
