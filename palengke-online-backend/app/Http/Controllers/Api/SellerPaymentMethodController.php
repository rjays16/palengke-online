<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SellerPaymentMethod;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;

class SellerPaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $methods = SellerPaymentMethod::where('seller_registration_id', $seller->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($method) {
                return [
                    'id' => $method->id,
                    'type' => $method->type,
                    'account_name' => $method->account_name,
                    'account_number' => $method->account_number,
                    'is_default' => $method->is_default,
                ];
            });

        return response()->json(['payment_methods' => $methods]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'type' => 'required|string',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'agreed' => 'required|boolean|accepted',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $method = SellerPaymentMethod::create([
            'seller_registration_id' => $seller->id,
            'type' => $validated['type'],
            'account_name' => $validated['account_name'],
            'account_number' => $validated['account_number'],
            'is_default' => !SellerPaymentMethod::where('seller_registration_id', $seller->id)->exists(),
        ]);

        return response()->json([
            'message' => 'Payment method added successfully.',
            'payment_method' => [
                'id' => $method->id,
                'type' => $method->type,
                'account_name' => $method->account_name,
                'account_number' => $method->account_number,
                'is_default' => $method->is_default,
            ],
        ]);
    }

    public function destroy($id)
    {
        $method = SellerPaymentMethod::findOrFail($id);
        $method->delete();

        return response()->json(['message' => 'Payment method removed successfully.']);
    }

    public function setDefault($id)
    {
        $method = SellerPaymentMethod::findOrFail($id);

        SellerPaymentMethod::where('seller_registration_id', $method->seller_registration_id)
            ->update(['is_default' => false]);

        $method->update(['is_default' => true]);

        return response()->json(['message' => 'Default payment method updated.']);
    }
}
