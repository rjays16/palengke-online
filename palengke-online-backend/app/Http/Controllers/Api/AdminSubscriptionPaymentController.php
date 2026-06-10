<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class AdminSubscriptionPaymentController extends Controller
{
    public function index(Request $request)
    {
        // Fetch subscription payments
        $subQuery = SubscriptionPayment::with('seller');

        // Fetch wallet top-ups (positive amounts only)
        $topUpQuery = WalletTransaction::with('seller')
            ->where('type', 'top_up');

        if ($request->search) {
            $s = $request->search;
            $subQuery->whereHas('seller', fn($q) => $q->where('store_name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
            $topUpQuery->whereHas('seller', fn($q) => $q->where('store_name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }

        $subscriptions = $subQuery->orderBy('created_at', 'desc')->get()->map(fn($p) => [
            'type' => 'subscription',
            'id' => 'sub-' . $p->id,
            'seller_id' => 'SELLER-' . strtoupper(dechex($p->seller_registration_id)),
            'store_name' => $p->seller?->store_name ?? 'Unknown',
            'seller_email' => $p->seller?->email ?? '',
            'plan_name' => $p->plan_name,
            'amount' => (float) $p->amount,
            'payment_method_name' => $p->payment_method_name ?? '—',
            'status' => $p->status,
            'date' => $p->subscribed_at?->toDateTimeString(),
            'period' => ($p->plan_start?->toDateString() ?? '') . ' → ' . ($p->plan_end?->toDateString() ?? ''),
        ]);

        $topUps = $topUpQuery->orderBy('created_at', 'desc')->get()->map(fn($t) => [
            'type' => 'top_up',
            'id' => 'topup-' . $t->id,
            'seller_id' => 'SELLER-' . strtoupper(dechex($t->seller_registration_id)),
            'store_name' => $t->seller?->store_name ?? 'Unknown',
            'seller_email' => $t->seller?->email ?? '',
            'plan_name' => 'Wallet Top-Up',
            'amount' => (float) $t->amount,
            'payment_method_name' => $t->payment_method_name ?? '—',
            'status' => 'completed',
            'date' => $t->created_at?->toDateTimeString(),
            'period' => '—',
        ]);

        $all = collect($subscriptions)
            ->merge($topUps)
            ->sortByDesc('date')
            ->values();

        return response()->json([
            'payments' => $all,
            'total' => $all->sum('amount'),
        ]);
    }
}
