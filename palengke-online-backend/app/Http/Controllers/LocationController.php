<?php

namespace App\Http\Controllers;

use App\Models\ServiceLocation;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $location = ServiceLocation::first();

        if (! $location) {
            return response()->json([
                'locations' => null,
            ]);
        }

        return response()->json([
            'locations' => $location->data,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'locations' => 'required|array',
        ]);

        $location = ServiceLocation::first();

        if (! $location) {
            $location = ServiceLocation::create([
                'data' => $validated['locations'],
            ]);
        } else {
            $location->update([
                'data' => $validated['locations'],
            ]);
        }

        return response()->json([
            'message' => 'Locations saved successfully',
            'locations' => $location->data,
        ]);
    }
}
