<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerDevice;
use App\Models\CustomerRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerDeviceController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'device_id' => 'required|string',
            'device_name' => 'nullable|string',
            'pin' => 'required|string|min:4|max:6',
        ]);

        $customer = CustomerRegistration::where('email', $validated['email'])->first();

        if (!$customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        $customer->update(['pin' => Hash::make($validated['pin'])]);

        $token = $customer->createToken('customer-device-token');

        $device = CustomerDevice::updateOrCreate(
            [
                'customer_registration_id' => $customer->id,
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

        $customer = CustomerRegistration::where('email', $validated['email'])->first();

        if (!$customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        if (!$customer->pin) {
            return response()->json(['message' => 'No PIN set for this account.'], 404);
        }

        if (!Hash::check($validated['pin'], $customer->pin)) {
            return response()->json(['message' => 'Invalid PIN.'], 401);
        }

        $device = CustomerDevice::firstOrCreate(
            [
                'customer_registration_id' => $customer->id,
                'device_id' => $validated['device_id'],
            ],
            [
                'device_name' => request()->userAgent(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        $token = $customer->createToken('customer-device-token');
        $device->update([
            'device_token_id' => $token->accessToken->id,
            'last_used_at' => now(),
        ]);

        return response()->json([
            'message' => 'PIN verified successfully.',
            'token' => $token->plainTextToken,
            'user' => [
                'id' => $customer->id,
                'email' => $customer->email,
                'firstName' => $customer->first_name,
                'lastName' => $customer->last_name,
            ],
        ]);
    }

    public function status(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'device_id' => 'required|string',
        ]);

        $customer = CustomerRegistration::where('email', $validated['email'])->first();

        if (!$customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        return response()->json([
            'hasPin' => $customer->pin !== null,
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

        $customer = CustomerRegistration::where('email', $validated['email'])->first();

        if (!$customer) {
            return response()->json(['message' => 'Customer not found.'], 404);
        }

        if (!$customer->pin) {
            return response()->json(['message' => 'No PIN set for this account.'], 404);
        }

        if (!Hash::check($validated['current_pin'], $customer->pin)) {
            return response()->json(['message' => 'Current PIN is incorrect.'], 401);
        }

        $customer->update(['pin' => Hash::make($validated['new_pin'])]);

        return response()->json([
            'message' => 'PIN changed successfully.',
        ]);
    }
}
