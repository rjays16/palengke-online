<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Models\SellerRegistration;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;

class AdminSubscriptionController extends Controller
{
    const PLANS = [
        '1m' => ['name' => '1 Month', 'months' => 1, 'price' => 600],
        '2m' => ['name' => '2 Months', 'months' => 2, 'price' => 1200],
        '4m' => ['name' => '4 Months', 'months' => 4, 'price' => 2100],
        '8m' => ['name' => '8 Months', 'months' => 8, 'price' => 4000],
    ];

    public function changePlan(Request $request, $id)
    {
        $validated = $request->validate([
            'plan_id' => 'required|string|in:1m,2m,4m,8m',
        ]);

        $sellerId = $this->resolveSellerId($id);
        $seller = SellerRegistration::findOrFail($sellerId);
        $plan = self::PLANS[$validated['plan_id']];

        $now = now();

        $seller->plan_id = $validated['plan_id'];
        $seller->plan_started_at = $now;
        $seller->plan_ends_at = $now->copy()->addMonths($plan['months']);
        $seller->subscription_status = 'active';
        $seller->save();

        SubscriptionPayment::create([
            'seller_registration_id' => $seller->id,
            'plan_id' => $validated['plan_id'],
            'plan_name' => $plan['name'],
            'amount' => $plan['price'],
            'payment_method_id' => null,
            'payment_method_name' => 'Admin',
            'status' => 'completed',
            'subscribed_at' => $now,
            'plan_start' => $now->toDateString(),
            'plan_end' => $now->copy()->addMonths($plan['months'])->toDateString(),
        ]);

        $adminName = $request->user()?->name ?? 'An admin';

        Notification::create([
            'type' => 'subscription_change',
            'title' => 'Plan Changed (Admin)',
            'message' => $adminName . ' changed ' . $seller->store_name . '\'s subscription to ' . $plan['name'] . '.',
            'link' => '/sellers/' . 'SELLER-' . strtoupper(dechex($seller->id)),
            'data' => [
                'seller_id' => $seller->id,
                'plan_id' => $validated['plan_id'],
                'plan_name' => $plan['name'],
                'action' => 'change_plan',
            ],
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Plan changed to ' . $plan['name'] . ' successfully.',
            'subscription' => [
                'plan' => $plan['name'],
                'amount' => $plan['price'],
                'start' => $now->toDateString(),
                'end' => $now->copy()->addMonths($plan['months'])->toDateString(),
                'status' => 'active',
            ],
        ]);
    }

    public function extend(Request $request, $id)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $sellerId = $this->resolveSellerId($id);
        $seller = SellerRegistration::findOrFail($sellerId);

        $currentEnd = $seller->plan_ends_at ? $seller->plan_ends_at->copy() : now();
        $newEnd = $currentEnd->addDays((int) $validated['days']);

        $seller->plan_ends_at = $newEnd;
        if (!$seller->plan_id) {
            $seller->plan_id = '1m';
        }
        if ($seller->subscription_status === 'cancelled' || $seller->subscription_status === 'expired') {
            $seller->subscription_status = 'active';
        }
        $seller->save();

        $adminName = $request->user()?->name ?? 'An admin';

        Notification::create([
            'type' => 'subscription_extend',
            'title' => 'Subscription Extended (Admin)',
            'message' => $adminName . ' extended ' . $seller->store_name . '\'s subscription by ' . $validated['days'] . ' days.',
            'link' => '/sellers/' . 'SELLER-' . strtoupper(dechex($seller->id)),
            'data' => [
                'seller_id' => $seller->id,
                'days' => (int) $validated['days'],
                'new_end' => $newEnd->toDateString(),
                'action' => 'extend',
            ],
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Subscription extended by ' . $validated['days'] . ' days.',
            'subscription' => [
                'plan' => $seller->plan_id ? (self::PLANS[$seller->plan_id]['name'] ?? 'Custom') : 'Trial',
                'amount' => $seller->plan_id ? (self::PLANS[$seller->plan_id]['price'] ?? 0) : 0,
                'start' => $seller->plan_started_at?->toDateString(),
                'end' => $newEnd->toDateString(),
                'status' => $seller->subscription_status,
            ],
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $sellerId = $this->resolveSellerId($id);
        $seller = SellerRegistration::findOrFail($sellerId);

        if ($seller->subscription_status === 'cancelled') {
            return response()->json(['message' => 'Subscription is already cancelled.'], 400);
        }

        $seller->subscription_status = 'cancelled';
        $seller->save();

        $adminName = $request->user()?->name ?? 'An admin';

        Notification::create([
            'type' => 'subscription_cancel',
            'title' => 'Subscription Cancelled (Admin)',
            'message' => $adminName . ' cancelled ' . $seller->store_name . '\'s subscription.',
            'link' => '/sellers/' . 'SELLER-' . strtoupper(dechex($seller->id)),
            'data' => [
                'seller_id' => $seller->id,
                'action' => 'cancel',
            ],
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Subscription cancelled.',
            'subscription' => [
                'plan' => $seller->plan_id ? (self::PLANS[$seller->plan_id]['name'] ?? 'Trial') : 'Trial',
                'amount' => $seller->plan_id ? (self::PLANS[$seller->plan_id]['price'] ?? 0) : 0,
                'start' => $seller->plan_started_at?->toDateString(),
                'end' => $seller->plan_ends_at?->toDateString(),
                'status' => 'cancelled',
            ],
        ]);
    }

    private function resolveSellerId($id): int
    {
        if (is_numeric($id)) return (int) $id;
        if (str_starts_with($id, 'SELLER-')) return hexdec(substr($id, 7));
        return (int) $id;
    }
}
