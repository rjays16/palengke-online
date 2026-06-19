<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use App\Models\DriverRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class DriverAuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $driver = DriverRegistration::where('email', $validated['email'])->first();

        if (!$driver) {
            return response()->json([
                'message' => 'No account found with this email.',
            ], 404);
        }

        if ($driver->status !== 'approved') {
            $message = $driver->status === 'rejected'
                ? 'Your registration was not approved. Please register again with updated information.'
                : 'Your account is not yet approved. Please wait for admin approval.';

            return response()->json([
                'message' => $message,
                'status' => $driver->status,
            ], 403);
        }

        return response()->json([
            'message' => 'Email verified.',
            'hasPin' => $driver->pin !== null,
        ]);
    }

    public function forgotPin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $driver = DriverRegistration::where('email', $validated['email'])->first();

        if (!$driver) {
            return response()->json([
                'message' => 'No account found with this email.',
            ], 404);
        }

        $otp = (string) random_int(100000, 999999);

        Cache::put('otp_' . $validated['email'], $otp, 300);

        Mail::to($validated['email'])->send(new OtpVerificationMail($otp));

        return response()->json([
            'message' => 'Verification code sent to your email for PIN reset.',
        ]);
    }
}
