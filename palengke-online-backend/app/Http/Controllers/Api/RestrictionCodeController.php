<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestrictionCode;
use Illuminate\Http\Request;

class RestrictionCodeController extends Controller
{
    public function index()
    {
        $codes = RestrictionCode::where('enabled', true)
            ->orderBy('code')
            ->get(['code', 'label', 'vehicle_types']);

        return response()->json([
            'restriction_codes' => $codes,
        ]);
    }

    public function adminIndex()
    {
        $codes = RestrictionCode::orderBy('code')->get();

        return response()->json([
            'restriction_codes' => $codes,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'label' => 'sometimes|required|string|max:255',
            'enabled' => 'sometimes|boolean',
            'vehicle_types' => 'sometimes|array',
            'vehicle_types.*' => 'string|max:100',
        ]);

        $code = RestrictionCode::findOrFail($id);
        $code->update($validated);

        return response()->json([
            'message' => 'Restriction code updated successfully.',
            'restriction_code' => $code->fresh(),
        ]);
    }

    public function toggle(Request $request, $id)
    {
        $code = RestrictionCode::findOrFail($id);
        $code->update([
            'enabled' => $request->input('enabled', !$code->enabled),
        ]);

        return response()->json([
            'message' => 'Restriction code updated successfully.',
            'restriction_code' => $code,
        ]);
    }
}
