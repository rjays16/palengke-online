<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $query = Country::query();

        // If the request is from the seller app (no auth), only return enabled countries
        if (! $request->user()) {
            $query->where('enabled', true);
        }

        $countries = $query->orderBy('name')->get();

        return response()->json([
            'countries' => $countries,
        ]);
    }

    public function toggle(Request $request, $id)
    {
        $country = Country::findOrFail($id);
        $country->update([
            'enabled' => $request->input('enabled', ! $country->enabled),
        ]);

        return response()->json([
            'message' => 'Country updated successfully',
            'country' => $country,
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'countries' => 'required|array',
            'countries.*.id' => 'required|integer|exists:countries,id',
            'countries.*.enabled' => 'required|boolean',
        ]);

        foreach ($validated['countries'] as $item) {
            Country::where('id', $item['id'])->update(['enabled' => $item['enabled']]);
        }

        return response()->json([
            'message' => 'Countries updated successfully',
        ]);
    }
}
