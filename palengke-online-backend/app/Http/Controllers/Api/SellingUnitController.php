<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SellingUnit;

class SellingUnitController extends Controller
{
    public function index()
    {
        $units = SellingUnit::where('enabled', true)
            ->orderBy('sort_order')
            ->get(['code', 'label', 'plural', 'icon', 'has_min_order', 'example_stock']);

        return response()->json([
            'selling_units' => $units,
        ]);
    }
}
