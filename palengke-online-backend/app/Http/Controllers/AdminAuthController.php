<?php

namespace App\Http\Controllers;

use App\Mail\AdminResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['This account is inactive.'],
            ]);
        }

        if (!in_array($user->role, ['admin', 'superadmin'])) {
            throw ValidationException::withMessages([
                'email' => ['Unauthorized role.'],
            ]);
        }

        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json([
                'message' => 'No admin account found with this email.',
            ], 404);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'This account is inactive.',
            ], 403);
        }

        // Delete any existing tokens for this email
        DB::table('admin_password_resets')->where('email', $validated['email'])->delete();

        $token = Str::random(60);

        DB::table('admin_password_resets')->insert([
            'email' => $validated['email'],
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $resetUrl = $request->input('reset_url')
            ? rtrim($request->input('reset_url'), '/') . '/reset-password?token=' . $token . '&email=' . urlencode($validated['email'])
            : 'http://localhost:3000/reset-password?token=' . $token . '&email=' . urlencode($validated['email']);

        Mail::to($validated['email'])->send(new AdminResetPasswordMail(
            $resetUrl,
            $user->name,
            $validated['email'],
        ));

        return response()->json([
            'message' => 'Password reset link has been sent to your email.',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $record = DB::table('admin_password_resets')
            ->where('email', $validated['email'])
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'Invalid or expired password reset token.',
            ], 400);
        }

        if (!Hash::check($validated['token'], $record->token)) {
            return response()->json([
                'message' => 'Invalid or expired password reset token.',
            ], 400);
        }

        // Check if token is expired (60 minutes)
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->diffInMinutes(now()) > 60) {
            DB::table('admin_password_resets')->where('email', $validated['email'])->delete();
            return response()->json([
                'message' => 'Password reset token has expired. Please request a new one.',
            ], 400);
        }

        $user = User::where('email', $validated['email'])->first();
        $user->update(['password' => Hash::make($validated['password'])]);

        // Delete the used token
        DB::table('admin_password_resets')->where('email', $validated['email'])->delete();

        return response()->json([
            'message' => 'Password has been reset successfully.',
        ]);
    }
}
