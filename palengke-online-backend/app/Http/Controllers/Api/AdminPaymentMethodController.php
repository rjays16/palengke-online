<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class AdminPaymentMethodController extends Controller
{
    public function index()
    {
        return response()->json(
            PaymentMethod::orderBy('sort_order')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'visible' => 'boolean',
            'coming_soon' => 'boolean',
        ]);

        $method = PaymentMethod::create($validated);

        return response()->json($method, 201);
    }

    public function update(Request $request, $id)
    {
        $method = PaymentMethod::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'visible' => 'boolean',
            'coming_soon' => 'boolean',
        ]);

        $method->update($validated);

        return response()->json($method);
    }

    public function destroy($id)
    {
        $method = PaymentMethod::findOrFail($id);
        $method->delete();

        return response()->json(['message' => 'Deleted.']);
    }
}
