<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SellerDevice;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SellerDeviceController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'device_id' => 'required|string',
            'device_name' => 'nullable|string',
            'pin' => 'required|string|min:4|max:6',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        if ($seller->status !== 'approved') {
            return response()->json(['message' => 'Account not yet approved.'], 403);
        }

        $device = SellerDevice::updateOrCreate(
            [
                'seller_registration_id' => $seller->id,
                'device_id' => $validated['device_id'],
            ],
            [
                'device_name' => $validated['device_name'] ?? request()->userAgent(),
                'pin' => Hash::make($validated['pin']),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Device registered successfully.',
            'device' => [
                'id' => $device->id,
                'device_id' => $device->device_id,
            ],
        ]);
    }

    public function verifyPin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'device_id' => 'required|string',
            'pin' => 'required|string',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $device = SellerDevice::where('seller_registration_id', $seller->id)
            ->where('device_id', $validated['device_id'])
            ->first();

        if (!$device || !$device->pin) {
            return response()->json(['message' => 'No PIN set for this device.'], 404);
        }

        if (!Hash::check($validated['pin'], $device->pin)) {
            return response()->json(['message' => 'Invalid PIN.'], 401);
        }

        $device->update(['last_used_at' => now()]);

        $token = $seller->createToken('seller-pin-token')->plainTextToken;

        return response()->json([
            'message' => 'PIN verified successfully.',
            'token' => $token,
            'user' => [
                'id' => $seller->id,
                'email' => $seller->email,
                'firstName' => $seller->first_name,
                'lastName' => $seller->last_name,
                'storeName' => $seller->store_name,
            ],
        ]);
    }

    public function status(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'device_id' => 'required|string',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $device = SellerDevice::where('seller_registration_id', $seller->id)
            ->where('device_id', $validated['device_id'])
            ->first();

        return response()->json([
            'hasPin' => $device && $device->pin !== null,
        ]);
    }
}
