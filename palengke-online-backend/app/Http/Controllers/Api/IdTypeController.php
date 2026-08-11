<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IdType;
use Illuminate\Http\Request;

class IdTypeController extends Controller
{
    public function index()
    {
        $types = IdType::where('enabled', true)->get(['key', 'name', 'group']);

        $order = [
            'passport', 'drivers_license', 'umid', 'sss', 'philhealth', 'pagibig', 'postal',
            'voters', 'tin', 'national_id', 'senior_citizen', 'pwd', 'owwa', 'seaman',
            'prc', 'company', 'student',
        ];

        $types = $types->sortBy(fn ($type) => array_search($type->key, $order) ?? PHP_INT_MAX)->values();

        return response()->json([
            'id_types' => $types,
        ]);
    }
}
