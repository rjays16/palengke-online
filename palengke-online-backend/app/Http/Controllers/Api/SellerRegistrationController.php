<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SellerRegistrationController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            // Step 1
            'email' => 'required|email|unique:seller_registrations,email',
            'country_code' => 'required|string',
            'phone' => 'required|string',
            'full_phone' => 'required|string',
            'email_verified' => 'boolean',
            'password' => 'required|string|min:8',

            // Step 2
            'first_name' => 'required|string|min:2',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string|min:2',
            'country' => 'required|string',
            'birthdate' => 'required|date',
            'birthplace' => 'required|string|min:3',
            'current_address' => 'required|string|min:10',

            // Step 3
            'store_name' => 'required|string|min:3',
            'region' => 'required|string',
            'city' => 'required|string',
            'barangay' => 'required|string',
            'store_place' => 'required|string|min:5',
            'complete_address' => 'required|string|min:15',

            // Step 4
            'verification_choice' => 'nullable|string|in:verify,skip',
            'verification_status' => 'nullable|string',

            // Step 5
            'id_type' => 'nullable|string',
            'id_type_name' => 'nullable|string',

            // Steps 6 & 7
            'id_front_photo' => 'nullable|string',
            'id_back_photo' => 'nullable|string',
            'selfie_photo' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Save base64 images as files
        foreach (['id_front_photo', 'id_back_photo', 'selfie_photo'] as $field) {
            if (!empty($validated[$field])) {
                $validated[$field] = $this->saveBase64Image($validated[$field], $field);
            }
        }

        $registration = SellerRegistration::create($validated);

        Notification::create([
            'type' => 'new_seller',
            'message' => "New seller registration: {$validated['store_name']}",
            'link' => '/approvals',
            'data' => ['registration_id' => $registration->id],
        ]);

        return response()->json([
            'message' => 'Registration submitted successfully. Your application is pending review.',
            'registration' => $registration,
        ], 201);
    }

    private function saveBase64Image(string $dataUrl, string $fieldName): string
    {
        if (!preg_match('/^data:image\/(\w+);base64,(.+)$/', $dataUrl, $matches)) {
            return $dataUrl;
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $base64Data = $matches[2];
        $decoded = base64_decode($base64Data);

        if ($decoded === false) {
            return $dataUrl;
        }

        $filename = 'seller-photos/' . uniqid() . '-' . $fieldName . '.' . $extension;
        Storage::disk('public')->put($filename, $decoded);

        return Storage::disk('public')->url($filename);
    }

    public function pending()
    {
        $registrations = SellerRegistration::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($r) => $this->formatForAdmin($r));

        return response()->json([
            'pending' => $registrations,
        ]);
    }

    public function history()
    {
        $registrations = SellerRegistration::whereIn('status', ['approved', 'rejected'])
            ->orderBy('reviewed_at', 'desc')
            ->get()
            ->map(fn ($r) => $this->formatForAdmin($r));

        return response()->json([
            'history' => $registrations,
        ]);
    }

    public function approve($id)
    {
        $registration = SellerRegistration::findOrFail($id);

        if ($registration->status !== 'pending') {
            return response()->json(['message' => 'Registration is not pending.'], 400);
        }

        $registration->update([
            'status' => 'approved',
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Registration approved successfully.',
            'registration' => $this->formatForAdmin($registration->fresh()),
        ]);
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:3',
        ]);

        $registration = SellerRegistration::findOrFail($id);

        if ($registration->status !== 'pending') {
            return response()->json(['message' => 'Registration is not pending.'], 400);
        }

        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['reason'],
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Registration rejected.',
            'registration' => $this->formatForAdmin($registration->fresh()),
        ]);
    }

    private function formatForAdmin($r)
    {
        return [
            'id' => 'SELLER-' . strtoupper(dechex($r->id)),
            'role' => 'seller',
            'storeName' => $r->store_name,
            'category' => '',
            'address' => $r->complete_address,
            'contact' => $r->full_phone,
            'firstName' => $r->first_name,
            'lastName' => $r->last_name,
            'email' => $r->email,
            'validIdPhoto' => $r->id_front_photo,
            'selfiePhoto' => $r->selfie_photo,
            'businessPermitPhoto' => null,
            'status' => $r->status,
            'submittedAt' => $r->created_at->toDateString(),
            'reviewedAt' => $r->reviewed_at?->toDateString(),
            'reviewedBy' => $r->reviewedBy?->name ?? null,
            'rejectReason' => $r->rejection_reason,
            'storeName' => $r->store_name,
            'region' => $r->region,
            'city' => $r->city,
            'barangay' => $r->barangay,
            'storePlace' => $r->store_place,
            'completeAddress' => $r->complete_address,
            'firstName' => $r->first_name,
            'lastName' => $r->last_name,
            'middleName' => $r->middle_name,
            'birthdate' => $r->birthdate,
            'birthplace' => $r->birthplace,
            'currentAddress' => $r->current_address,
            'idType' => $r->id_type,
            'idTypeName' => $r->id_type_name,
        ];
    }

    public function show($id)
    {
        $registration = SellerRegistration::findOrFail($id);
        return response()->json([
            'registration' => $this->formatForAdmin($registration),
        ]);
    }
}
