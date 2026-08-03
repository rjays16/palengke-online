<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DriverRegistration;
use App\Models\Notification;
use App\Models\Order;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;

class TipController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_id' => 'nullable|integer',
            'driver_id' => 'nullable|integer',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'delivery_address' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'items' => 'nullable|array',
            'items.*.name' => 'nullable|string',
            'items.*.price' => 'nullable|numeric',
            'items.*.quantity' => 'nullable|integer|min:1',
            'delivery_fee' => 'nullable|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'message' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|string|max:255',
        ]);

        $seller = !empty($validated['seller_id'])
            ? SellerRegistration::find($validated['seller_id'])
            : null;

        if (!$seller) {
            $seller = SellerRegistration::where('status', 'approved')->first();
        }

        if (!$seller) {
            return response()->json(['message' => 'No seller is currently available.'], 422);
        }

        $driver = !empty($validated['driver_id'])
            ? DriverRegistration::find($validated['driver_id'])
            : null;

        $items = $validated['items'] ?? [];
        $subtotal = array_reduce($items, function ($sum, $item) {
            return $sum + ((float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 1));
        }, 0);

        $deliveryFee = (float) ($validated['delivery_fee'] ?? 0);
        $tipAmount = (float) $validated['amount'];
        $customerName = $validated['customer_name'] ?? 'Customer';

        $order = Order::create([
            'seller_registration_id' => $seller->id,
            'driver_registration_id' => $driver->id ?? null,
            'customer_name' => $customerName,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'delivery_address' => $validated['delivery_address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'items' => $items,
            'delivery_fee' => $deliveryFee,
            'total' => round($subtotal + $deliveryFee, 2),
            'status' => 'pending',
            'payment_method' => $validated['payment_method'] ?? null,
            'tip_amount' => $tipAmount,
            'tip_message' => $validated['message'] ?? null,
            'tip_customer_name' => $customerName,
        ]);

        if ($driver && $tipAmount > 0) {
            Notification::create([
                'driver_registration_id' => $driver->id,
                'type' => 'tip',
                'title' => 'You Got a Tip!',
                'category' => 'earnings',
                'message' => $customerName . ' tipped you ₱' . number_format($tipAmount, 2) . '.',
                'data' => [
                    'order_id' => $order->id,
                    'amount' => $tipAmount,
                    'customer_name' => $customerName,
                    'message' => $validated['message'] ?? null,
                    'delivery_fee' => $deliveryFee,
                ],
            ]);
        }

        return response()->json([
            'message' => 'Order placed successfully.',
            'order' => [
                'id' => $order->id,
                'total' => (float) $order->total,
                'tip_amount' => $tipAmount,
            ],
        ], 201);
    }

    public function latest(Request $request)
    {
        $driver = $request->user();

        $order = Order::with('seller')
            ->where('driver_registration_id', $driver->id)
            ->whereNotNull('tip_amount')
            ->where('tip_amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        $totalTipsToday = (float) Order::where('driver_registration_id', $driver->id)
            ->whereNotNull('tip_amount')
            ->where('tip_amount', '>', 0)
            ->whereDate('created_at', today())
            ->sum('tip_amount');

        if (!$order) {
            return response()->json(['message' => 'No tips yet.'], 404);
        }

        return response()->json([
            'tip' => [
                'order_id' => $order->id,
                'amount' => (float) $order->tip_amount,
                'customer_name' => $order->tip_customer_name ?? $order->customer_name ?? 'Customer',
                'message' => $order->tip_message,
                'delivery_fee' => (float) ($order->delivery_fee ?? 0),
                'store_name' => $order->seller?->store_name ?? $order->seller?->first_name ?? null,
            ],
            'total_tips_today' => $totalTipsToday,
        ]);
    }
}
