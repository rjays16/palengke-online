<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
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

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();

        if (!$seller || !Hash::check($validated['password'], $seller->password)) {
            return response()->json([
                'message' => 'Invalid email or password.',
            ], 401);
        }

        if ($seller->status !== 'approved') {
            return response()->json([
                'message' => 'Your account is not yet approved. Please wait for admin approval.',
            ], 403);
        }

        $token = $seller->createToken('seller-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
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

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        if (!Hash::check($validated['current_password'], $seller->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 401);
        }

        $seller->update(['password' => Hash::make($validated['new_password'])]);

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }
}
