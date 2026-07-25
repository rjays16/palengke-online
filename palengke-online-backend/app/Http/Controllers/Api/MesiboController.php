<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SellerRegistration;
use App\Models\DriverRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MesiboController extends Controller
{
    public function getToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'user_type' => 'required|in:seller,driver',
        ]);

        $email = $request->input('email');
        $userType = $request->input('user_type');

        if ($userType === 'seller') {
            $user = SellerRegistration::where('email', $email)->first();
            $name = $user ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) : $email;
        } else {
            $user = DriverRegistration::where('email', $email)->first();
            $name = $user ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) : $email;
        }

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (empty($name)) {
            $name = $email;
        }

        $appToken = config('services.mesibo.app_token');
        $appId = config('services.mesibo.app_id');

        if (!$appToken || !$appId) {
            return response()->json(['message' => 'Mesibo not configured'], 500);
        }

        $response = Http::post('https://api.mesibo.com/backend/', [
            'op' => 'useradd',
            'token' => $appToken,
            'user' => [
                'address' => $email,
                'name' => $name,
                'token' => [
                    'appid' => $appId,
                    'expiry' => 525600,
                ],
            ],
        ]);

        $data = $response->json();

        if (!$response->successful() || !isset($data['user']['token'])) {
            return response()->json([
                'message' => 'Failed to create Mesibo user',
                'error' => $data['message'] ?? 'Unknown error',
                'raw' => $data,
            ], 500);
        }

        $userToken = $data['user']['token'];

        return response()->json([
            'token' => $userToken,
            'name' => $name,
            'address' => $email,
        ]);
    }
}
