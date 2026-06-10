<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SellerRegistration;
use App\Models\Notification;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    const TRIAL_DAYS = 25;
    const PLANS = [
        '1m' => ['name' => '1 Month', 'months' => 1, 'price' => 600],
        '2m' => ['name' => '2 Months', 'months' => 2, 'price' => 1200],
        '4m' => ['name' => '4 Months', 'months' => 4, 'price' => 2100],
        '8m' => ['name' => '8 Months', 'months' => 8, 'price' => 4000],
    ];

    public function index(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        // Auto-start trial if not yet started
        if (!$seller->trial_started_at) {
            $seller->trial_started_at = now();
            $seller->trial_ends_at = now()->addDays(self::TRIAL_DAYS);
            $seller->subscription_status = 'trial';
            $seller->save();
        }

        $status = $this->computeStatus($seller);

        return response()->json([
            'status' => $status,
            'trial_start' => $seller->trial_started_at?->toDateString(),
            'trial_end' => $seller->trial_ends_at?->toDateString(),
            'trial_days_left' => $seller->trial_ends_at ? max(0, now()->startOfDay()->diffInDays($seller->trial_ends_at, false)) : 0,
            'plan_id' => $seller->plan_id,
            'plan_start' => $seller->plan_started_at?->toDateString(),
            'plan_end' => $seller->plan_ends_at?->toDateString(),
            'plan_days_left' => $seller->plan_ends_at ? max(0, now()->startOfDay()->diffInDays($seller->plan_ends_at, false)) : 0,
            'can_request_banner' => $seller->plan_id === '8m' && $status === 'active',
        ]);
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'plan_id' => 'required|string|in:1m,2m,4m,8m',
            'payment_method_id' => 'nullable|integer|exists:payment_methods,id',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $plan = self::PLANS[$validated['plan_id']];
        $price = $plan['price'];

        // Wallet balance check
        if (!empty($validated['payment_method_id'])) {
            $pm = PaymentMethod::find($validated['payment_method_id']);
            if ($pm && strtolower($pm->name) === 'palengke wallet') {
                if ((float) $seller->wallet_balance < $price) {
                    return response()->json([
                        'message' => 'Insufficient wallet balance.',
                        'error' => 'insufficient_balance',
                        'balance' => (float) $seller->wallet_balance,
                        'required' => $price,
                        'shortfall' => $price - (float) $seller->wallet_balance,
                    ], 422);
                }
                // Deduct balance
                $seller->wallet_balance -= $price;
            }
        }

        $now = now();

        $seller->plan_id = $validated['plan_id'];
        $seller->plan_started_at = $now;
        $seller->plan_ends_at = $now->copy()->addMonths($plan['months']);
        $seller->subscription_status = 'active';
        $seller->save();

        // Create admin notification
        $paymentLabel = '';
        if (!empty($validated['payment_method_id'])) {
            $pm = PaymentMethod::find($validated['payment_method_id']);
            $paymentLabel = $pm ? ' via ' . $pm->name : '';
        }

        Notification::create([
            'seller_registration_id' => null,
            'type' => 'subscription',
            'title' => 'New Subscription',
            'message' => $seller->store_name . ' subscribed to ' . $plan['name'] . $paymentLabel . '.',
            'link' => '/admin/sellers/' . $seller->id,
            'data' => [
                'seller_id' => $seller->id,
                'plan_id' => $validated['plan_id'],
                'plan_name' => $plan['name'],
                'amount' => $plan['price'],
                'payment_method_id' => $validated['payment_method_id'] ?? null,
            ],
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Subscribed successfully!',
            'plan_id' => $seller->plan_id,
            'plan_start' => $seller->plan_started_at->toDateString(),
            'plan_end' => $seller->plan_ends_at->toDateString(),
            'balance' => (float) $seller->wallet_balance,
        ]);
    }

    private function computeStatus($seller): string
    {
        if ($seller->subscription_status === 'active') {
            if ($seller->plan_ends_at && now()->greaterThan($seller->plan_ends_at)) {
                $seller->subscription_status = 'expired';
                $seller->save();
                return 'expired';
            }
            return 'active';
        }

        if ($seller->subscription_status === 'trial') {
            if ($seller->trial_ends_at && now()->greaterThan($seller->trial_ends_at)) {
                $seller->subscription_status = 'expired';
                $seller->save();
                return 'expired';
            }
            return 'trial';
        }

        return $seller->subscription_status ?? 'expired';
    }
}
