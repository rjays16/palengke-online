<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DriverRegistration;
use App\Models\DriverWalletTransaction;
use App\Models\Notification;
use Illuminate\Http\Request;

class AdminDriverWalletController extends Controller
{
    public function topUp(Request $request, $id)
    {
        if ($request->user()?->role !== 'superadmin') {
            return response()->json(['message' => 'Super admin access required.'], 403);
        }
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $driverId = $this->resolveDriverId($id);
        $driver = DriverRegistration::findOrFail($driverId);

        $amount = (float) $validated['amount'];

        $driver->wallet_balance += $amount;
        $driver->save();

        DriverWalletTransaction::create([
            'driver_registration_id' => $driver->id,
            'type' => 'top_up',
            'amount' => $amount,
            'payment_method_id' => null,
            'payment_method_name' => 'Admin',
            'notes' => 'Admin top-up',
        ]);

        $adminName = $request->user()?->name ?? 'An admin';
        $driverName = trim($driver->first_name . ' ' . $driver->last_name) ?: 'Driver';

        Notification::create([
            'type' => 'wallet_top_up',
            'title' => 'Wallet Top-Up (Admin)',
            'message' => $adminName . ' added ₱' . number_format($amount) . ' to ' . $driverName . '\'s wallet.',
            'link' => '/drivers/' . 'DRIVER-' . strtoupper(dechex($driver->id)),
            'category' => 'wallet',
            'driver_registration_id' => $driver->id,
            'data' => [
                'driver_id' => $driver->id,
                'amount' => $amount,
            ],
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Wallet topped up successfully!',
            'balance' => (float) $driver->wallet_balance,
            'amount_added' => $amount,
        ]);
    }

    public function transactions(Request $request, $id)
    {
        $driverId = $this->resolveDriverId($id);
        $driver = DriverRegistration::findOrFail($driverId);

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

    private function resolveDriverId($id): int
    {
        if (is_numeric($id)) return (int) $id;
        if (str_starts_with($id, 'DRIVER-')) return hexdec(substr($id, 7));
        return (int) $id;
    }
}
