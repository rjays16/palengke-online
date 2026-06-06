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

        // Save PIN to seller account (not per-device)
        $seller->update(['pin' => Hash::make($validated['pin'])]);

        // Create token for this device
        $token = $seller->createToken('device-token');

        // Register/update the device
        $device = SellerDevice::updateOrCreate(
            [
                'seller_registration_id' => $seller->id,
                'device_id' => $validated['device_id'],
            ],
            [
                'device_name' => $validated['device_name'] ?? request()->userAgent(),
                'device_token_id' => $token->accessToken->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'PIN set successfully.',
            'token' => $token->plainTextToken,
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

        if (!$seller->pin) {
            return response()->json(['message' => 'No PIN set for this account.'], 404);
        }

        if (!Hash::check($validated['pin'], $seller->pin)) {
            return response()->json(['message' => 'Invalid PIN.'], 401);
        }

        // Verify the device exists, or create it
        $device = SellerDevice::firstOrCreate(
            [
                'seller_registration_id' => $seller->id,
                'device_id' => $validated['device_id'],
            ],
            [
                'device_name' => request()->userAgent(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        // Create a new token for this session
        $token = $seller->createToken('device-token');
        $device->update([
            'device_token_id' => $token->accessToken->id,
            'last_used_at' => now(),
        ]);

        return response()->json([
            'message' => 'PIN verified successfully.',
            'token' => $token->plainTextToken,
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

        return response()->json([
            'hasPin' => $seller->pin !== null,
        ]);
    }

    public function list(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $currentDeviceId = $request->input('current_device_id');

        $devices = SellerDevice::where('seller_registration_id', $seller->id)
            ->orderBy('last_used_at', 'desc')
            ->get()
            ->map(function ($device) use ($currentDeviceId) {
                return [
                    'id' => $device->id,
                    'device_id' => $device->device_id,
                    'device_name' => $device->device_name,
                    'ip_address' => $device->ip_address,
                    'last_used_at' => $device->last_used_at?->diffForHumans(),
                    'created_at' => $device->created_at->diffForHumans(),
                    'is_current' => $device->device_id === $currentDeviceId,
                ];
            });

        return response()->json([
            'hasPin' => $seller->pin !== null,
            'devices' => $devices,
        ]);
    }

    public function logoutCurrent(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $seller = SellerRegistration::where('email', $request->email)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $seller->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function remove(Request $request)
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

        if (!$device) {
            return response()->json(['message' => 'Device not found.'], 404);
        }

        // Revoke the associated token
        if ($device->device_token_id) {
            $seller->tokens()->where('id', $device->device_token_id)->delete();
        }

        $device->delete();

        return response()->json([
            'message' => 'Device removed successfully.',
        ]);
    }

    public function changePin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'device_id' => 'required|string',
            'current_pin' => 'required|string',
            'new_pin' => 'required|string|min:4|max:6',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        if (!$seller->pin) {
            return response()->json(['message' => 'No PIN set for this account.'], 404);
        }

        if (!Hash::check($validated['current_pin'], $seller->pin)) {
            return response()->json(['message' => 'Current PIN is incorrect.'], 401);
        }

        $seller->update(['pin' => Hash::make($validated['new_pin'])]);

        return response()->json([
            'message' => 'PIN changed successfully.',
        ]);
    }
}
