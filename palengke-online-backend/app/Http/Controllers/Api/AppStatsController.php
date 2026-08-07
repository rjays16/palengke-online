<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DriverRegistration;
use App\Models\Order;
use App\Models\SellerRegistration;

class AppStatsController extends Controller
{
    public function index()
    {
        return response()->json([
            'active_drivers' => DriverRegistration::where('status', 'approved')->count(),
            'deliveries_done' => Order::whereIn('status', ['completed', 'delivered'])->count(),
            'active_sellers' => SellerRegistration::where('status', 'approved')->count(),
            'happy_customers' => Order::whereNotNull('customer_name')->distinct('customer_name')->count('customer_name'),
            'cities_served' => SellerRegistration::where('status', 'approved')->whereNotNull('city')->distinct('city')->count('city'),
        ]);
    }
}
