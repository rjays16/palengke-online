<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DriverPerformanceController extends Controller
{
    public function show(Request $request)
    {
        $driver = $request->user();
        $orders = $driver->orders()->get();

        $total = $orders->count();
        $completed = $orders->where('status', 'completed')->count();
        $cancelled = $orders->where('status', 'cancelled')->count();
        $processing = $orders->where('status', 'processing')->count();

        $completionRate = $total > 0 ? round(($completed / $total) * 100) : 0;
        $cancellationRate = $total > 0 ? round(($cancelled / $total) * 100) : 0;

        $improvements = [];

        if ($total === 0) {
            $improvements[] = [
                'id' => 1,
                'title' => 'Accept Orders',
                'description' => 'Start accepting delivery orders to build your record',
                'icon' => 'checkmarkCircleOutline',
                'score' => '—',
                'type' => 'warning',
            ];
        } else {
            $improvements[] = [
                'id' => 1,
                'title' => 'Completion Rate',
                'description' => $completionRate >= 90 ? 'Great job completing deliveries!' : 'Try to complete more assigned deliveries',
                'icon' => 'checkmarkCircleOutline',
                'score' => "{$completionRate}%",
                'type' => $completionRate >= 90 ? 'great' : 'good',
            ];

            $improvements[] = [
                'id' => 2,
                'title' => 'Cancellation Rate',
                'description' => $cancellationRate <= 5 ? 'Excellent — very few cancellations' : 'Try to reduce cancellations',
                'icon' => 'closeCircleOutline',
                'score' => "{$cancellationRate}%",
                'type' => $cancellationRate <= 5 ? 'great' : ($cancellationRate <= 15 ? 'good' : 'warning'),
            ];

            $improvements[] = [
                'id' => 3,
                'title' => 'Total Deliveries',
                'description' => $total >= 50 ? 'Consistent delivery activity' : 'Keep taking orders to grow your experience',
                'icon' => 'cubeOutline',
                'score' => (string) $total,
                'type' => $total >= 100 ? 'great' : ($total >= 25 ? 'good' : 'warning'),
            ];
        }

        return response()->json([
            'totalTrips' => $total,
            'completedTrips' => $completed,
            'cancelledTrips' => $cancelled,
            'processingTrips' => $processing,
            'completionRate' => $completionRate,
            'cancellationRate' => $cancellationRate,
            'improvements' => $improvements,
        ]);
    }
}
