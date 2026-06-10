<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;

class AdminSubscriptionPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = SubscriptionPayment::with('seller')
            ->orderBy('created_at', 'desc');

        if ($request->search) {
            $s = $request->search;
            $query->whereHas('seller', fn($q) => $q->where('store_name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->plan_id) {
            $query->where('plan_id', $request->plan_id);
        }

        $payments = $query->get()->map(fn($p) => [
            'id' => $p->id,
            'seller_id' => 'SELLER-' . strtoupper(dechex($p->seller_registration_id)),
            'store_name' => $p->seller?->store_name ?? 'Unknown',
            'seller_email' => $p->seller?->email ?? '',
            'plan_id' => $p->plan_id,
            'plan_name' => $p->plan_name,
            'amount' => (float) $p->amount,
            'payment_method_name' => $p->payment_method_name ?? '—',
            'status' => $p->status,
            'subscribed_at' => $p->subscribed_at?->toDateTimeString(),
            'plan_start' => $p->plan_start?->toDateString(),
            'plan_end' => $p->plan_end?->toDateString(),
        ]);

        return response()->json([
            'payments' => $payments,
            'total' => $payments->sum('amount'),
        ]);
    }
}
