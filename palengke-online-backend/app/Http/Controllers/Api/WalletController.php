<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function balance(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        return response()->json([
            'balance' => (float) $seller->wallet_balance,
        ]);
    }
}
