<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return response()->json(
            PaymentMethod::where('visible', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'description', 'color', 'coming_soon'])
        );
    }
}
