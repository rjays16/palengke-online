<?php

namespace App\Http\Controllers;

use App\Models\Country;
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

    public function publicIndex()
    {
        $location = ServiceLocation::first();

        if (! $location) {
            return response()->json([
                'locations' => null,
            ]);
        }

        $locations = $location->data;

        $country = Country::where('code', 'PH')->first();
        if ($country) {
            $regions = $country->regions()->get();
            foreach ($locations as &$region) {
                $cr = $regions->firstWhere('name', $region['name']);
                if ($cr && !$cr->enabled) {
                    $region['checked'] = false;
                    $region['indeterminate'] = false;
                    foreach ($region['cities'] as &$city) {
                        $city['checked'] = false;
                        $city['indeterminate'] = false;
                        foreach ($city['barangays'] as &$b) {
                            $b['checked'] = false;
                        }
                    }
                }
            }
        }

        return response()->json([
            'locations' => $locations,
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
