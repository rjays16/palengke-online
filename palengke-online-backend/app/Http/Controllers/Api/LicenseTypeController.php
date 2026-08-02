<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LicenseType;
use Illuminate\Http\Request;

class LicenseTypeController extends Controller
{
    public function index()
    {
        $types = LicenseType::where('enabled', true)
            ->orderBy('name')
            ->get(['name', 'description']);

        return response()->json([
            'license_types' => $types,
        ]);
    }
}
