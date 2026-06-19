<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DriverDevice;
use App\Models\DriverRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DriverDeviceController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'device_id' => 'required|string',
            'device_name' => 'nullable|string',
            'pin' => 'required|string|min:4|max:6',
        ]);

        $driver = DriverRegistration::where('email', $validated['email'])->first();

        if (!$driver) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        if ($driver->status !== 'approved') {
            return response()->json(['message' => 'Account not yet approved.'], 403);
        }

        $driver->update(['pin' => Hash::make($validated['pin'])]);

        $token = $driver->createToken('device-token');

        $device = DriverDevice::updateOrCreate(
            [
                'driver_registration_id' => $driver->id,
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

        $driver = DriverRegistration::where('email', $validated['email'])->first();

        if (!$driver) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        if (!$driver->pin) {
            return response()->json(['message' => 'No PIN set for this account.'], 404);
        }

        if (!Hash::check($validated['pin'], $driver->pin)) {
            return response()->json(['message' => 'Invalid PIN.'], 401);
        }

        $device = DriverDevice::firstOrCreate(
            [
                'driver_registration_id' => $driver->id,
                'device_id' => $validated['device_id'],
            ],
            [
                'device_name' => request()->userAgent(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        $token = $driver->createToken('device-token');
        $device->update([
            'device_token_id' => $token->accessToken->id,
            'last_used_at' => now(),
        ]);

        return response()->json([
            'message' => 'PIN verified successfully.',
            'token' => $token->plainTextToken,
            'user' => [
                'id' => $driver->id,
                'email' => $driver->email,
                'firstName' => $driver->first_name,
                'lastName' => $driver->last_name,
            ],
        ]);
    }

    public function status(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'device_id' => 'required|string',
        ]);

        $driver = DriverRegistration::where('email', $validated['email'])->first();

        if (!$driver) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        return response()->json([
            'hasPin' => $driver->pin !== null,
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

        $driver = DriverRegistration::where('email', $validated['email'])->first();

        if (!$driver) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        if (!$driver->pin) {
            return response()->json(['message' => 'No PIN set for this account.'], 404);
        }

        if (!Hash::check($validated['current_pin'], $driver->pin)) {
            return response()->json(['message' => 'Current PIN is incorrect.'], 401);
        }

        $driver->update(['pin' => Hash::make($validated['new_pin'])]);

        return response()->json([
            'message' => 'PIN changed successfully.',
        ]);
    }
}
