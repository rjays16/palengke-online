<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Licence;
use App\Models\DriverRegistration;
use App\Models\DriverWalletTransaction;
use App\Models\Notification;
use App\Models\PaymentMethod;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class LicenceController extends Controller
{
    private function getLicenceFee(): float
    {
        $fee = SystemSetting::getValue('licence_fee');
        return $fee ? (float) $fee : 2000.00;
    }

    private function isLicenceExpired(Licence $licence): bool
    {
        return $licence->is_paid && $licence->paid_until && $licence->paid_until->isPast();
    }

    private function serializeLicence(Licence $licence): array
    {
        return [
            'id' => $licence->id,
            'licenceId' => $licence->licence_id,
            'trialStartDate' => $licence->trial_started_at?->toISOString(),
            'trialEndDate' => $licence->trial_ends_at?->toISOString(),
            'isPaid' => $licence->is_paid,
            'paidDate' => $licence->paid_at?->toISOString(),
            'paidUntil' => $licence->paid_until?->toISOString(),
            'isExpired' => $this->isLicenceExpired($licence),
            'amount' => (float) $licence->amount,
            'isFree' => $licence->is_free,
        ];
    }

    public function show(Request $request)
    {
        $driver = $request->user();

        if (!$driver) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        $fee = $this->getLicenceFee();
        $licence = Licence::where('driver_registration_id', $driver->id)->first();

        if (!$licence) {
            $now = now();
            $trialEnd = $now->copy()->addDays(30);

            $licence = Licence::create([
                'driver_registration_id' => $driver->id,
                'licence_id' => 'PL-2026-' . strtoupper(dechex($driver->id)),
                'trial_started_at' => $now,
                'trial_ends_at' => $trialEnd,
                'is_paid' => false,
                'paid_at' => null,
                'amount' => $fee,
            ]);
        } elseif ((float) $licence->amount !== $fee) {
            $licence->update(['amount' => $fee]);
        }

        return response()->json([
            'licence' => $this->serializeLicence($licence),
        ]);
    }

    public function pay(Request $request)
    {
        $validated = $request->validate([
            'payment_method_id' => 'sometimes|integer|exists:payment_methods,id',
        ]);

        $driver = $request->user();

        if (!$driver) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        $licence = Licence::where('driver_registration_id', $driver->id)->first();

        if (!$licence) {
            return response()->json(['message' => 'No licence found. Please try again.'], 404);
        }

        if ($licence->is_free) {
            return response()->json(['message' => 'Licence is free — no payment needed.'], 400);
        }

        if ($licence->is_paid && !$this->isLicenceExpired($licence)) {
            return response()->json(['message' => 'Licence is already paid.'], 400);
        }

        $fee = $this->getLicenceFee();
        $paymentMethodName = null;
        $paymentMethod = null;

        if (!empty($validated['payment_method_id'])) {
            $paymentMethod = PaymentMethod::find($validated['payment_method_id']);
            $paymentMethodName = $paymentMethod?->name;
        }

        $isWalletPayment = $paymentMethod && strtolower($paymentMethod->name) === 'palengke wallet';

        if ($isWalletPayment) {
            if ((float) $driver->wallet_balance < $fee) {
                return response()->json([
                    'message' => 'Insufficient wallet balance. Please top up your Palengke Wallet.',
                    'balance' => (float) $driver->wallet_balance,
                ], 422);
            }

            $driver->wallet_balance -= $fee;
            $driver->save();

            DriverWalletTransaction::create([
                'driver_registration_id' => $driver->id,
                'type' => 'licence_fee',
                'amount' => $fee,
                'payment_method_id' => $paymentMethod->id,
                'payment_method_name' => $paymentMethod->name,
                'notes' => 'Palengke Online licence fee (1 year)',
            ]);
        }

        $licence->update([
            'is_paid' => true,
            'paid_at' => now(),
            'paid_until' => now()->addYear(),
            'amount' => $fee,
            'payment_method_name' => $paymentMethodName,
        ]);

        $licence->refresh();

        return response()->json([
            'message' => 'Payment successful.',
            'licence' => $this->serializeLicence($licence),
            'balance' => $isWalletPayment ? (float) $driver->wallet_balance : null,
            'paid_with_wallet' => $isWalletPayment,
        ]);
    }

    private function resolveDriverId($id): int
    {
        if (is_numeric($id)) {
            return (int) $id;
        }
        if (str_starts_with($id, 'DRIVER-')) {
            return hexdec(substr($id, 7));
        }
        return (int) $id;
    }

    public function adminShow(Request $request, $id)
    {
        $driver = DriverRegistration::find($this->resolveDriverId($id));
        if (!$driver) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        $licence = Licence::where('driver_registration_id', $driver->id)->first();

        if (!$licence) {
            return response()->json(['licence' => null]);
        }

        return response()->json([
            'licence' => $this->serializeLicence($licence),
        ]);
    }

    public function toggleFree(Request $request, $id)
    {
        $driver = DriverRegistration::find($this->resolveDriverId($id));
        if (!$driver) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        $licence = Licence::where('driver_registration_id', $driver->id)->first();

        if (!$licence) {
            $fee = $this->getLicenceFee();
            $now = now();
            $licence = Licence::create([
                'driver_registration_id' => $driver->id,
                'licence_id' => 'PL-2026-' . strtoupper(dechex($driver->id)),
                'trial_started_at' => $now,
                'trial_ends_at' => $now->copy()->addDays(30),
                'is_paid' => false,
                'paid_at' => null,
                'amount' => $fee,
                'is_free' => true,
            ]);
        } else {
            $licence->update([
                'is_free' => !$licence->is_free,
            ]);
        }

        $isFree = $licence->fresh()->is_free;
        $name = trim($driver->firstName . ' ' . $driver->lastName) ?: 'Driver #' . $driver->id;

        Notification::create([
            'type' => 'licence_free_toggle',
            'title' => $isFree ? 'Free Licence Enabled' : 'Free Licence Disabled',
            'message' => $isFree
                ? "{$name} is now marked as Free Licence — no payment needed."
                : "{$name} is no longer Free Licence — standard payment applies.",
            'link' => "/drivers/DRIVER-{$driver->id}",
        ]);

        return response()->json([
            'isFree' => $isFree,
        ]);
    }
}
