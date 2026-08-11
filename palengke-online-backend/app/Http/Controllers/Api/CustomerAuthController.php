<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use App\Models\CustomerRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class CustomerAuthController extends Controller
{
    public function checkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $existing = CustomerRegistration::where('email', $validated['email'])->first();

        if (!$existing) {
            return response()->json(['available' => true]);
        }

        return response()->json([
            'available' => false,
            'status' => $existing->status,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'country_code' => 'required|string',
            'phone' => 'required|string',
            'full_phone' => 'required|string',
            'email_verified' => 'boolean',

            'first_name' => 'required|string|min:2',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string|min:2',
            'birthdate' => 'required|date',

            'region' => 'required|string',
            'city' => 'required|string',
            'barangay' => 'required|string',
            'street' => 'required|string',
            'landmark' => 'nullable|string',
            'address_label' => 'nullable|string',

            'accept_marketing' => 'boolean',
            'terms_accepted' => 'boolean',
        ]);

        if (CustomerRegistration::where('email', $validated['email'])->exists()) {
            return response()->json([
                'message' => 'This email is already registered.',
            ], 422);
        }

        $validated['status'] = 'approved';

        $customer = CustomerRegistration::create($validated);

        $token = $customer->createToken('customer-token');

        return response()->json([
            'message' => 'Registration successful.',
            'token' => $token->plainTextToken,
            'user' => [
                'id' => $customer->id,
                'email' => $customer->email,
                'firstName' => $customer->first_name,
                'middleName' => $customer->middle_name,
                'lastName' => $customer->last_name,
                'phone' => $customer->full_phone,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $customer = CustomerRegistration::where('email', $validated['email'])->first();

        if (!$customer) {
            return response()->json([
                'message' => 'No account found with this email.',
            ], 404);
        }

        return response()->json([
            'message' => 'Email verified.',
            'hasPin' => $customer->pin !== null,
        ]);
    }

    public function forgotPin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $customer = CustomerRegistration::where('email', $validated['email'])->first();

        if (!$customer) {
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
