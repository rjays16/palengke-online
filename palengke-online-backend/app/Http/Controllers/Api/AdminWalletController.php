<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SellerRegistration;
use App\Models\PaymentMethod;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class AdminWalletController extends Controller
{
    public function topUp(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
        ]);

        $sellerId = $this->resolveSellerId($id);
        $seller = SellerRegistration::findOrFail($sellerId);

        $pm = PaymentMethod::findOrFail($validated['payment_method_id']);
        $pmName = strtolower($pm->name);

        // Block COD and Palengke Wallet for top-ups
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
            'notes' => 'Admin top-up via ' . $pm->name,
        ]);

        Notification::create([
            'type' => 'wallet_top_up',
            'title' => 'Wallet Top-Up (Admin)',
            'message' => $seller->store_name . ' was topped up ₱' . number_format($amount) . ' via ' . $pm->name . '.',
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

    public function transactions(Request $request, $id)
    {
        $sellerId = $this->resolveSellerId($id);
        $seller = SellerRegistration::findOrFail($sellerId);

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

    private function resolveSellerId($id): int
    {
        if (is_numeric($id)) return (int) $id;
        if (str_starts_with($id, 'SELLER-')) return hexdec(substr($id, 7));
        return (int) $id;
    }
}
