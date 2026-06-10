<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'period' => 'nullable|in:today,week,month,year',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $seller = SellerRegistration::where('email', $request->email)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        if ($request->start_date && $request->end_date) {
            $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();
            $prevRangeDays = $startDate->diffInDays($endDate) + 1;
            $prevStartDate = (clone $startDate)->subDays($prevRangeDays);
        } else {
            $period = $request->period ?? 'week';
            $startDate = match($period) {
                'today' => now()->startOfDay(),
                'week' => now()->startOfWeek(),
                'month' => now()->startOfMonth(),
                'year' => now()->startOfYear(),
                default => now()->startOfWeek(),
            };
            $prevStartDate = match($period) {
                'today' => now()->subDay()->startOfDay(),
                'week' => now()->subWeek()->startOfWeek(),
                'month' => now()->subMonth()->startOfMonth(),
                'year' => now()->subYear()->startOfYear(),
                default => now()->subWeek()->startOfWeek(),
            };
            $endDate = null;
        }

        $allOrdersQuery = Order::where('seller_registration_id', $seller->id)
            ->where('created_at', '>=', $startDate);
        if ($endDate) {
            $allOrdersQuery->where('created_at', '<=', $endDate);
        }
        $allOrders = $allOrdersQuery->orderBy('created_at', 'desc')->get();

        $prevOrders = Order::where('seller_registration_id', $seller->id)
            ->where('created_at', '>=', $prevStartDate)
            ->where('created_at', '<', $startDate)
            ->get();

        $completedOrders = $allOrders->where('status', 'completed');
        $totalOrders = $allOrders->count();
        $completedCount = $completedOrders->count();
        $totalSales = $allOrders->sum('total');

        $prevTotalSales = $prevOrders->sum('total');
        $prevTotalOrders = $prevOrders->count();

        $salesGrowth = $prevTotalSales > 0 ? round(($totalSales - $prevTotalSales) / $prevTotalSales * 100, 1) : ($totalSales > 0 ? 100 : 0);
        $ordersGrowth = $prevTotalOrders > 0 ? round(($totalOrders - $prevTotalOrders) / $prevTotalOrders * 100, 1) : ($totalOrders > 0 ? 100 : 0);

        $avgOrderValue = $totalOrders > 0 ? round($totalSales / $totalOrders) : 0;
        $prevAvgOrderValue = $prevTotalOrders > 0 ? round($prevTotalSales / $prevTotalOrders) : 0;
        $avgOrderValueGrowth = $prevAvgOrderValue > 0 ? round(($avgOrderValue - $prevAvgOrderValue) / $prevAvgOrderValue * 100, 1) : 0;

        $completionRate = $totalOrders > 0 ? round($completedCount / $totalOrders * 100) : 0;
        $customers = $allOrders->pluck('customer_name')->unique()->filter()->count();

        // Chart data based on period
        $chartData = [];
        if ($request->start_date && $request->end_date) {
            $days = (int) \Carbon\Carbon::parse($request->start_date)->startOfDay()->diffInDays(\Carbon\Carbon::parse($request->end_date)->startOfDay()) + 1;
            for ($i = 0; $i < $days; $i++) {
                $date = \Carbon\Carbon::parse($request->start_date)->addDays($i);
                $dayOrders = $allOrders->filter(fn($o) => $o->created_at && $o->created_at->format('Y-m-d') === $date->format('Y-m-d'));
                $chartData[] = [
                    'day' => $date->format('M d'),
                    'sales' => (int) $dayOrders->sum('total'),
                    'orders' => $dayOrders->count(),
                ];
            }
        } else {
            switch ($period) {
                case 'today':
                    for ($i = 23; $i >= 0; $i--) {
                        $hour = now()->startOfDay()->addHours($i);
                        $nextHour = (clone $hour)->addHour();
                        $hourOrders = $allOrders->filter(fn($o) => $o->created_at && $o->created_at >= $hour && $o->created_at < $nextHour);
                        $chartData[] = [
                            'day' => $hour->format('H:00'),
                            'sales' => (int) $hourOrders->sum('total'),
                            'orders' => $hourOrders->count(),
                        ];
                    }
                    break;
                case 'month':
                    $daysInMonth = now()->startOfMonth()->diffInDays(now()->endOfMonth()) + 1;
                    for ($i = $daysInMonth - 1; $i >= 0; $i--) {
                        $date = now()->startOfMonth()->addDays($i);
                        $dayOrders = $allOrders->filter(fn($o) => $o->created_at && $o->created_at->format('Y-m-d') === $date->format('Y-m-d'));
                        $chartData[] = [
                            'day' => $date->format('d'),
                            'sales' => (int) $dayOrders->sum('total'),
                            'orders' => $dayOrders->count(),
                        ];
                    }
                    break;
                case 'year':
                    for ($i = 11; $i >= 0; $i--) {
                        $month = now()->startOfYear()->addMonths($i);
                        $nextMonth = (clone $month)->addMonth();
                        $monthOrders = $allOrders->filter(fn($o) => $o->created_at && $o->created_at >= $month && $o->created_at < $nextMonth);
                        $chartData[] = [
                            'day' => $month->format('M'),
                            'sales' => (int) $monthOrders->sum('total'),
                            'orders' => $monthOrders->count(),
                        ];
                    }
                    break;
                default: // week
                    for ($i = 6; $i >= 0; $i--) {
                        $date = now()->subDays($i);
                        $dayOrders = $allOrders->filter(fn($o) => $o->created_at && $o->created_at->format('Y-m-d') === $date->format('Y-m-d'));
                        $chartData[] = [
                            'day' => $date->format('D'),
                            'sales' => (int) $dayOrders->sum('total'),
                            'orders' => $dayOrders->count(),
                        ];
                    }
                    break;
            }
        }

        // Top products — aggregate from order items
        $productCounts = [];
        foreach ($allOrders as $order) {
            $items = $order->items ?? [];
            foreach ($items as $item) {
                $name = $item['name'] ?? 'Unknown';
                $qty = is_numeric($item['qty'] ?? 0) ? (float) $item['qty'] : 0;
                $price = (float) ($item['price'] ?? 0);
                if (!isset($productCounts[$name])) {
                    $productCounts[$name] = ['sold' => 0, 'revenue' => 0];
                }
                $productCounts[$name]['sold'] += $qty;
                $productCounts[$name]['revenue'] += $qty * $price;
            }
        }
        arsort($productCounts);
        $maxRevenue = !empty($productCounts) ? max(array_column($productCounts, 'revenue')) : 1;
        $topProducts = [];
        $i = 0;
        foreach ($productCounts as $name => $data) {
            if ($i >= 5) break;
            $topProducts[] = [
                'id' => $i + 1,
                'name' => $name,
                'sold' => (int) $data['sold'],
                'revenue' => (int) $data['revenue'],
                'percentage' => $maxRevenue > 0 ? (int) round($data['revenue'] / $maxRevenue * 100) : 0,
            ];
            $i++;
        }

        // Category sales — map product names to categories
        $categoryMap = [];
        $products = Product::where('seller_registration_id', $seller->id)->get()->keyBy('name');
        foreach ($allOrders as $order) {
            $items = $order->items ?? [];
            foreach ($items as $item) {
                $name = $item['name'] ?? 'Unknown';
                $qty = is_numeric($item['qty'] ?? 0) ? (float) $item['qty'] : 0;
                $price = (float) ($item['price'] ?? 0);
                $total = $qty * $price;
                $cat = isset($products[$name]) && $products[$name]->category ? $products[$name]->category : 'Other';
                if (!isset($categoryMap[$cat])) {
                    $categoryMap[$cat] = ['sales' => 0, 'orders' => 0, 'orderIds' => []];
                }
                $categoryMap[$cat]['sales'] += $total;
                if (!in_array($order->id, $categoryMap[$cat]['orderIds'])) {
                    $categoryMap[$cat]['orders']++;
                    $categoryMap[$cat]['orderIds'][] = $order->id;
                }
            }
        }
        $totalCatSales = array_sum(array_column($categoryMap, 'sales')) ?: 1;
        $catColors = ['green', 'orange', 'red', 'blue', 'purple', 'teal'];
        $categorySales = [];
        $ci = 0;
        arsort($categoryMap);
        foreach ($categoryMap as $name => $data) {
            $categorySales[] = [
                'name' => $name,
                'sales' => (int) $data['sales'],
                'orders' => $data['orders'],
                'percentage' => round($data['sales'] / $totalCatSales * 100),
                'color' => $catColors[$ci % count($catColors)],
            ];
            $ci++;
        }

        // Recent activity
        $recentActivity = $allOrders->take(10)->map(function ($order) {
            $typeMap = [
                'completed' => 'sale',
                'cancelled' => 'cancelled',
                'pending' => 'sale',
                'processing' => 'sale',
            ];
            return [
                'type' => $typeMap[$order->status] ?? 'sale',
                'text' => $order->status === 'cancelled'
                    ? "Order #{$order->id} cancelled by customer"
                    : ($order->status === 'completed'
                        ? "Order #{$order->id} completed"
                        : "New order from {$order->customer_name}"),
                'time' => $order->created_at ? $order->created_at->diffForHumans() : 'recent',
                'amount' => (int) $order->total,
            ];
        });

        // Monthly goals (always current month, not filtered by period)
        $monthStart = now()->startOfMonth();
        $monthOrders = Order::where('seller_registration_id', $seller->id)
            ->where('created_at', '>=', $monthStart)
            ->get();
        $monthSales = $monthOrders->sum('total');
        $monthOrderCount = $monthOrders->count();

        // Compute real rating from reviews
        $avgRating = Review::where('seller_registration_id', $seller->id)->avg('rating');
        $avgRating = $avgRating ? round($avgRating, 1) : 0;

        // Compute real delivery time from completed orders (all time)
        $allCompletedOrders = Order::where('seller_registration_id', $seller->id)
            ->where('status', 'completed')
            ->get();
        $avgDeliveryTime = $allCompletedOrders->count() > 0
            ? (int) round($allCompletedOrders->avg(fn($o) => $o->created_at ? $o->updated_at->diffInMinutes($o->created_at) : 0))
            : 0;

        $monthlyGoals = [
            'salesTarget' => (float) ($seller->monthly_sales_target ?: 50000),
            'ordersTarget' => (int) ($seller->monthly_orders_target ?: 100),
            'satisfactionTarget' => 5.0,
            'currentSales' => (int) $monthSales,
            'currentOrders' => $monthOrderCount,
        ];

        return response()->json([
            'stats' => [
                'totalSales' => $totalSales,
                'salesGrowth' => $salesGrowth,
                'totalOrders' => $totalOrders,
                'ordersGrowth' => $ordersGrowth,
                'avgOrderValue' => $avgOrderValue,
                'avgOrderValueGrowth' => $avgOrderValueGrowth,
                'completionRate' => $completionRate,
                'avgDeliveryTime' => $avgDeliveryTime,
                'rating' => $avgRating,
                'customers' => $customers,
            ],
            'chartData' => $chartData,
            'topProducts' => $topProducts,
            'categorySales' => $categorySales,
            'recentActivity' => $recentActivity,
            'monthlyGoals' => $monthlyGoals,
        ]);
    }

    public function updateGoals(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'monthly_sales_target' => 'required|numeric|min:0|max:99999999',
            'monthly_orders_target' => 'required|integer|min:0|max:999999',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $seller->update([
            'monthly_sales_target' => $validated['monthly_sales_target'],
            'monthly_orders_target' => $validated['monthly_orders_target'],
        ]);

        return response()->json([
            'message' => 'Goals updated successfully!',
            'monthly_sales_target' => (float) $seller->monthly_sales_target,
            'monthly_orders_target' => (int) $seller->monthly_orders_target,
        ]);
    }
}
