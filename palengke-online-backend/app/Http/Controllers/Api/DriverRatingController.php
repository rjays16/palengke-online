<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DriverRating;
use App\Models\Order;
use Illuminate\Http\Request;

class DriverRatingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'driver_registration_id' => 'required|exists:driver_registrations,id',
            'customer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        $existing = DriverRating::where('order_id', $order->id)
            ->where('driver_registration_id', $validated['driver_registration_id'])
            ->first();
        if ($existing) {
            return response()->json(['message' => 'This delivery has already been rated.'], 409);
        }

        $rating = DriverRating::create([
            'driver_registration_id' => $validated['driver_registration_id'],
            'order_id' => $order->id,
            'customer_name' => $validated['customer_name'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'message' => 'Thank you for rating your driver!',
            'rating' => $rating,
        ], 201);
    }
}
