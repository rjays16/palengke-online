<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'customer_name' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        $existing = Review::where('order_id', $order->id)->first();
        if ($existing) {
            return response()->json(['message' => 'This order has already been rated.'], 409);
        }

        $review = Review::create([
            'order_id' => $order->id,
            'seller_registration_id' => $order->seller_registration_id,
            'customer_name' => $validated['customer_name'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'message' => 'Thank you for your feedback!',
            'review' => $review,
        ], 201);
    }
}
