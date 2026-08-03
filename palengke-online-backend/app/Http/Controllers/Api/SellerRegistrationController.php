<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\AccountApprovedMail;
use App\Mail\AccountRejectedMail;
use App\Mail\RegistrationConfirmationMail;
use App\Models\Notification;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SellerRegistrationController extends Controller
{
    const PLANS = [
        '1m' => ['name' => '1 Month', 'months' => 1, 'price' => 600],
        '2m' => ['name' => '2 Months', 'months' => 2, 'price' => 1200],
        '4m' => ['name' => '4 Months', 'months' => 4, 'price' => 2100],
        '8m' => ['name' => '8 Months', 'months' => 8, 'price' => 4000],
    ];

    public function register(Request $request)
    {
        $validated = $request->validate([
            // Step 1
            'email' => [
                'required',
                'email',
                Rule::unique('seller_registrations', 'email')->where(fn ($q) => $q->where('status', '!=', 'rejected')),
            ],
            'country_code' => 'required|string',
            'phone' => 'required|string',
            'full_phone' => 'required|string',
            'email_verified' => 'boolean',

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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',

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

        Mail::to($validated['email'])->send(
            new RegistrationConfirmationMail(
                $validated['first_name'],
                $validated['store_name'],
            ),
        );

        return response()->json([
            'message' => 'Registration submitted successfully. Your application is pending review.',
            'registration' => $registration,
        ], 201);
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'id_type' => 'nullable|string',
            'id_type_name' => 'nullable|string',
            'id_front_photo' => 'nullable|string',
            'id_back_photo' => 'nullable|string',
            'selfie_photo' => 'nullable|string',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        foreach (['id_front_photo', 'id_back_photo', 'selfie_photo'] as $field) {
            if (!empty($validated[$field])) {
                $validated[$field] = $this->saveBase64Image($validated[$field], $field);
            }
        }

        $seller->update([
            'id_type' => $validated['id_type'] ?? $seller->id_type,
            'id_type_name' => $validated['id_type_name'] ?? $seller->id_type_name,
            'id_front_photo' => $validated['id_front_photo'] ?? $seller->id_front_photo,
            'id_back_photo' => $validated['id_back_photo'] ?? $seller->id_back_photo,
            'selfie_photo' => $validated['selfie_photo'] ?? $seller->selfie_photo,
            'verification_status' => 'verified',
            'verification_choice' => 'complete',
        ]);

        return response()->json([
            'message' => 'Verification submitted successfully.',
        ]);
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
        $registration = SellerRegistration::findOrFail($this->resolveId($id));

        if ($registration->status !== 'pending') {
            return response()->json(['message' => 'Registration is not pending.'], 400);
        }

        $registration->update([
            'status' => 'approved',
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
        ]);

        Mail::to($registration->email)->send(
            new AccountApprovedMail(
                $registration->first_name,
                $registration->store_name,
            ),
        );

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

        $registration = SellerRegistration::findOrFail($this->resolveId($id));

        if ($registration->status !== 'pending') {
            return response()->json(['message' => 'Registration is not pending.'], 400);
        }

        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['reason'],
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
        ]);

        Mail::to($registration->email)->send(
            new AccountRejectedMail(
                $registration->first_name,
                $registration->store_name,
                $validated['reason'],
            ),
        );

        return response()->json([
            'message' => 'Registration rejected.',
            'registration' => $this->formatForAdmin($registration->fresh()),
        ]);
    }

    public function index()
    {
        $registrations = SellerRegistration::withCount('products')
            ->with('products')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($r) => $this->formatForAdmin($r));

        return response()->json([
            'sellers' => $registrations,
        ]);
    }

    private function resolveId($id): int
    {
        if (is_numeric($id)) {
            return (int) $id;
        }

        if (str_starts_with($id, 'SELLER-')) {
            return hexdec(substr($id, 7));
        }

        return (int) $id;
    }

    private function formatForAdmin($r)
    {
        $plan = $r->plan_id ? (self::PLANS[$r->plan_id] ?? null) : null;

        return [
            'id' => 'SELLER-' . strtoupper(dechex($r->id)),
            'role' => 'seller',
            'storeName' => $r->store_name,
            'category' => $r->categories ? (is_array($r->categories) ? implode(', ', $r->categories) : $r->categories) : '',
            'address' => $r->complete_address,
            'contact' => $r->full_phone,
            'firstName' => $r->first_name,
            'lastName' => $r->last_name,
            'email' => $r->email,
            'validIdPhoto' => $r->id_front_photo,
            'idBackPhoto' => $r->id_back_photo,
            'selfiePhoto' => $r->selfie_photo,
            'businessPermitPhoto' => null,
            'status' => $r->status,
            'verificationStatus' => $r->verification_status,
            'verificationChoice' => $r->verification_choice,
            'submittedAt' => $r->created_at->toDateString(),
            'reviewedAt' => $r->reviewed_at?->toDateString(),
            'reviewedBy' => $r->reviewedBy?->name ?? null,
            'rejectReason' => $r->rejection_reason,
            'region' => $r->region,
            'city' => $r->city,
            'barangay' => $r->barangay,
            'storePlace' => $r->store_place,
            'completeAddress' => $r->complete_address,
            'middleName' => $r->middle_name,
            'birthdate' => $r->birthdate,
            'birthplace' => $r->birthplace,
            'currentAddress' => $r->current_address,
            'idType' => $r->id_type,
            'idTypeName' => $r->id_type_name,
            'walletBalance' => (float) ($r->wallet_balance ?? 0),
            'productCount' => $r->products_count ?? $r->products->count() ?? 0,
            'subscription' => [
                'plan' => $plan ? $plan['name'] : 'Trial',
                'amount' => $plan ? $plan['price'] : 0,
                'start' => $r->plan_started_at?->toDateString(),
                'end' => $r->plan_ends_at?->toDateString(),
                'status' => $r->subscription_status ?? ($r->status === 'approved' ? 'trial' : 'inactive'),
            ],
        ];
    }

    public function show($id)
    {
        $registration = SellerRegistration::findOrFail($this->resolveId($id));
        return response()->json([
            'registration' => $this->formatForAdmin($registration),
        ]);
    }
}
