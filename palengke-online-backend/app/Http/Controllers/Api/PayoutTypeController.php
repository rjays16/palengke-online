<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PayoutType;
use Illuminate\Http\Request;

class PayoutTypeController extends Controller
{
    public function index()
    {
        $types = PayoutType::where('enabled', true)->get(['key', 'name']);

        $order = [
            'gcash', 'bdo', 'bpi', 'metrobank', 'landbank', 'unionbank', 'pnb',
        ];

        $types = $types->sortBy(fn ($type) => array_search($type->key, $order) ?? PHP_INT_MAX)->values();

        return response()->json([
            'payout_types' => $types,
        ]);
    }
}
