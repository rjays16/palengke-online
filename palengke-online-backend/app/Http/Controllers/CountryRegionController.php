<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CountryRegion;
use Illuminate\Http\Request;

class CountryRegionController extends Controller
{
    public function index(Country $country)
    {
        $regions = $country->regions()->orderBy('name')->get();
        return response()->json(['regions' => $regions]);
    }

    public function toggle(Country $country, CountryRegion $region)
    {
        if ($region->country_id !== $country->id) {
            return response()->json(['message' => 'Region does not belong to this country'], 404);
        }

        $region->update(['enabled' => !$region->enabled]);

        return response()->json([
            'message' => 'Region updated successfully',
            'region' => $region->fresh(),
        ]);
    }
}
