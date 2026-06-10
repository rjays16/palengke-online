<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();

        $allOrders = Order::where('seller_registration_id', $seller->id)->get();

        $todayOrders = $allOrders->where('created_at', '>=', $today);
        $weekOrders = $allOrders->where('created_at', '>=', $weekStart);

        $totalProducts = Product::where('seller_registration_id', $seller->id)->count();

        return response()->json([
            'stats' => [
                'today_sales' => $todayOrders->sum('total'),
                'today_orders' => $todayOrders->count(),
                'total_products' => $totalProducts,
                'week_sales' => $weekOrders->sum('total'),
                'total_orders' => $allOrders->count(),
            ],
        ]);
    }
}
