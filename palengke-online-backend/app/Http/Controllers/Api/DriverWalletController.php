<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DriverRegistration;
use App\Models\DriverWalletTransaction;
use App\Models\Notification;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class DriverWalletController extends Controller
{
    private function driver(Request $request)
    {
        $user = $request->user();

        if (!$user instanceof DriverRegistration) {
            abort(401, 'Unauthenticated.');
        }

        return $user;
    }

    public function balance(Request $request)
    {
        return response()->json([
            'balance' => (float) $this->driver($request)->wallet_balance,
        ]);
    }

    public function topUp(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
        ]);

        $driver = $this->driver($request);
        $pm = PaymentMethod::findOrFail($validated['payment_method_id']);
        $pmName = strtolower($pm->name);

        if (in_array($pmName, ['cash on delivery', 'cod', 'palengke wallet'])) {
            return response()->json([
                'message' => 'Cannot top up using ' . $pm->name . '. Please use GCash, PayPal, or Stripe.',
            ], 422);
        }

        $amount = (float) $validated['amount'];
        $driver->wallet_balance += $amount;
        $driver->save();

        DriverWalletTransaction::create([
            'driver_registration_id' => $driver->id,
            'type' => 'top_up',
            'amount' => $amount,
            'payment_method_id' => $pm->id,
            'payment_method_name' => $pm->name,
            'notes' => 'Wallet top-up via ' . $pm->name,
        ]);

        Notification::create([
            'type' => 'wallet_top_up',
            'title' => 'Wallet Top-Up',
            'message' => 'Your wallet was topped up by ₱' . number_format($amount) . ' via ' . $pm->name . '.',
            'category' => 'wallet',
            'driver_registration_id' => $driver->id,
            'data' => [
                'amount' => $amount,
                'payment_method' => $pm->name,
            ],
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Wallet topped up successfully!',
            'balance' => (float) $driver->wallet_balance,
            'amount_added' => $amount,
            'payment_method' => $pm->name,
        ]);
    }

    public function transactions(Request $request)
    {
        $driver = $this->driver($request);

        $txns = DriverWalletTransaction::where('driver_registration_id', $driver->id)
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
            'balance' => (float) $driver->wallet_balance,
        ]);
    }
}
