<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $email = $validated['email'];
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put('otp_' . $email, $otp, 300);

        Mail::to($email)->send(new OtpVerificationMail($otp));

        return response()->json([
            'message' => 'Verification code sent successfully.',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $email = $validated['email'];
        $otp = $validated['otp'];

        $cachedOtp = Cache::get('otp_' . $email);

        if (!$cachedOtp) {
            return response()->json([
                'message' => 'Verification code has expired or was not found. Please request a new one.',
            ], 400);
        }

        if ($cachedOtp !== $otp) {
            return response()->json([
                'message' => 'Invalid verification code. Please try again.',
            ], 400);
        }

        Cache::forget('otp_' . $email);

        return response()->json([
            'message' => 'Email verified successfully.',
        ]);
    }
}
