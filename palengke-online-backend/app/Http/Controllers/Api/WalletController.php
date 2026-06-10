<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Models\SellerRegistration;
use App\Models\WalletTransaction;
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

    public function topUp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric|min:1',
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $pm = PaymentMethod::findOrFail($validated['payment_method_id']);
        $pmName = strtolower($pm->name);

        if (in_array($pmName, ['cash on delivery', 'cod', 'palengke wallet'])) {
            return response()->json([
                'message' => 'Cannot top up using ' . $pm->name . '. Please use GCash, PayPal, or Stripe.',
            ], 422);
        }

        $amount = (float) $validated['amount'];
        $seller->wallet_balance += $amount;
        $seller->save();

        WalletTransaction::create([
            'seller_registration_id' => $seller->id,
            'type' => 'top_up',
            'amount' => $amount,
            'payment_method_id' => $pm->id,
            'payment_method_name' => $pm->name,
            'notes' => 'Seller top-up via ' . $pm->name,
        ]);

        Notification::create([
            'type' => 'wallet_top_up',
            'title' => 'Wallet Top-Up',
            'message' => $seller->store_name . ' topped up ₱' . number_format($amount) . ' via ' . $pm->name . '.',
            'link' => '/sellers/' . 'SELLER-' . strtoupper(dechex($seller->id)),
            'data' => [
                'seller_id' => $seller->id,
                'amount' => $amount,
                'payment_method' => $pm->name,
            ],
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Wallet topped up successfully!',
            'balance' => (float) $seller->wallet_balance,
            'amount_added' => $amount,
            'payment_method' => $pm->name,
        ]);
    }

    public function transactions(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $txns = WalletTransaction::where('seller_registration_id', $seller->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'type' => $t->type,
                'amount' => (float) $t->amount,
                'payment_method_name' => $t->payment_method_name,
                'notes' => $t->notes,
                'created_at' => $t->created_at->toDateTimeString(),
            ]);

        return response()->json([
            'transactions' => $txns,
            'balance' => (float) $seller->wallet_balance,
        ]);
    }
}
