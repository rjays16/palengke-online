<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\DriverRegistrationConfirmationMail;
use App\Mail\DriverAccountApprovedMail;
use App\Mail\DriverAccountRejectedMail;
use App\Models\DriverDocument;
use App\Models\DriverRegistration;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class DriverRegistrationController extends Controller
{
    public function checkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $existing = DriverRegistration::where('email', $validated['email'])->first();

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
            'country' => 'required|string',
            'birthdate' => 'required|date',
            'birthplace' => 'required|string|min:3',
            'current_address' => 'required|string|min:10',

            'license_number' => 'required|string',
            'license_type' => 'required|string',
            'restriction_code' => 'required|string',
            'expiry_date' => 'required|date',

            'vehicle_type' => 'required|string',
            'plate_number' => 'required|string',
            'vehicle_model' => 'required|string',
            'vehicle_color' => 'nullable|string',

            'license_front_photo' => 'nullable|string',
            'license_back_photo' => 'nullable|string',
            'vehicle_reg_photo' => 'nullable|string',
            'nbi_clearance_photo' => 'nullable|string',
            'birth_cert_photo' => 'nullable|string',
            'selfie_photo' => 'nullable|string',
        ]);

        foreach (['license_front_photo', 'license_back_photo', 'vehicle_reg_photo', 'nbi_clearance_photo', 'birth_cert_photo', 'selfie_photo'] as $field) {
            if (!empty($validated[$field])) {
                $validated[$field] = $this->saveBase64Image($validated[$field], $field);
            }
        }

        $validated['status'] = 'pending';

        $registration = DriverRegistration::updateOrCreate(
            ['email' => $validated['email']],
            $validated
        );

        Notification::create([
            'type' => 'new_driver',
            'message' => "New driver registration: {$validated['first_name']} {$validated['last_name']}",
            'link' => '/approvals',
            'data' => ['registration_id' => $registration->id],
        ]);

        Mail::to($validated['email'])->send(
            new DriverRegistrationConfirmationMail(
                $validated['first_name'],
            ),
        );

        return response()->json([
            'message' => 'Registration submitted successfully. Your application is pending review.',
            'registration' => [
                'id' => $this->formatId($registration),
                'status' => $registration->status,
            ],
        ], 201);
    }

    public function uploadPhoto(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'photo_type' => 'required|string|in:license_front_photo,license_back_photo,vehicle_reg_photo,nbi_clearance_photo,birth_cert_photo,selfie_photo',
            'photo' => 'required|string',
        ]);

        $registration = DriverRegistration::where('email', $validated['email'])
            ->whereIn('status', ['draft', 'pending'])
            ->first();

        if (!$registration) {
            $registration = DriverRegistration::create([
                'email' => $validated['email'],
                'status' => 'draft',
                'country_code' => '',
                'phone' => '',
                'full_phone' => '',
                'first_name' => '',
                'last_name' => '',
                'country' => '',
                'birthdate' => now()->toDateString(),
                'birthplace' => '',
                'current_address' => '',
                'license_number' => '',
                'license_type' => '',
                'restriction_code' => '',
                'expiry_date' => now()->toDateString(),
                'vehicle_type' => '',
                'plate_number' => '',
                'vehicle_model' => '',
            ]);
        }

        $photoPath = $this->saveBase64Image($validated['photo'], $validated['photo_type']);
        $registration->update([$validated['photo_type'] => $photoPath]);

        $this->syncDocumentRecord($registration->email, $validated['photo_type'], $photoPath);

        return response()->json([
            'message' => 'Photo uploaded successfully.',
            'photo_url' => $photoPath,
        ]);
    }

    public function pending()
    {
        $registrations = DriverRegistration::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($r) => $this->formatForAdmin($r));

        return response()->json([
            'pending' => $registrations,
        ]);
    }

    public function history()
    {
        $registrations = DriverRegistration::whereIn('status', ['approved', 'rejected'])
            ->orderBy('reviewed_at', 'desc')
            ->get()
            ->map(fn ($r) => $this->formatForAdmin($r));

        return response()->json([
            'history' => $registrations,
        ]);
    }

    public function approve($id)
    {
        $registration = DriverRegistration::findOrFail($this->resolveId($id));

        if ($registration->status !== 'pending') {
            return response()->json(['message' => 'Registration is not pending.'], 400);
        }

        $registration->update([
            'status' => 'approved',
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
        ]);

        Mail::to($registration->email)->send(
            new DriverAccountApprovedMail($registration->first_name),
        );

        return response()->json([
            'message' => 'Driver approved successfully.',
            'registration' => $this->formatForAdmin($registration->fresh()),
        ]);
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:3',
        ]);

        $registration = DriverRegistration::findOrFail($this->resolveId($id));

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
            new DriverAccountRejectedMail($registration->first_name, $validated['reason']),
        );

        return response()->json([
            'message' => 'Driver registration rejected.',
            'registration' => $this->formatForAdmin($registration->fresh()),
        ]);
    }

    public function index()
    {
        $registrations = DriverRegistration::whereIn('status', ['approved', 'pending'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($r) => $this->formatForAdmin($r));

        return response()->json([
            'drivers' => $registrations,
        ]);
    }

    public function show($id)
    {
        $registration = DriverRegistration::findOrFail($this->resolveId($id));
        return response()->json([
            'registration' => $this->formatForAdmin($registration),
        ]);
    }

    public function deliveries($id)
    {
        $driver = DriverRegistration::findOrFail($this->resolveId($id));

        $orders = Order::where('driver_registration_id', $driver->id)
            ->with('seller')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer' => $order->customer_name,
                    'pickup' => $order->seller
                        ? ($order->seller->store_name ?: $order->seller->city ?: 'Store')
                        : 'Store',
                    'delivery' => $order->delivery_address,
                    'status' => $order->status,
                    'date' => $order->created_at ? $order->created_at->format('Y-m-d') : null,
                ];
            });

        return response()->json([
            'deliveries' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|min:1',
            'middleName' => 'nullable|string',
            'lastName' => 'required|string|min:1',
            'phone' => 'required|string',
            'vehicleType' => 'required|string',
            'vehicleModel' => 'nullable|string',
            'vehicleColor' => 'nullable|string',
            'plate' => 'required|string',
        ]);

        $registration = DriverRegistration::create([
            'first_name' => $validated['firstName'],
            'middle_name' => $validated['middleName'] ?? '',
            'last_name' => $validated['lastName'],
            'phone' => $validated['phone'],
            'full_phone' => $validated['phone'],
            'vehicle_type' => $validated['vehicleType'],
            'vehicle_model' => $validated['vehicleModel'] ?? '',
            'vehicle_color' => $validated['vehicleColor'] ?? '',
            'plate_number' => $validated['plate'],
            'email' => 'driver-' . uniqid() . '@temp.com',
            'country_code' => '+63',
            'country' => 'PH',
            'birthdate' => now()->toDateString(),
            'birthplace' => '',
            'current_address' => '',
            'license_number' => '',
            'license_type' => '',
            'restriction_code' => '',
            'expiry_date' => now()->toDateString(),
            'status' => 'approved',
        ]);

        return response()->json([
            'message' => 'Driver created successfully.',
            'driver' => $this->formatForAdmin($registration),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $registration = DriverRegistration::findOrFail($this->resolveId($id));

        $validated = $request->validate([
            'firstName' => 'required|string|min:1',
            'middleName' => 'nullable|string',
            'lastName' => 'required|string|min:1',
            'phone' => 'required|string',
            'vehicleType' => 'required|string',
            'vehicleModel' => 'nullable|string',
            'vehicleColor' => 'nullable|string',
            'plate' => 'required|string',
            'status' => 'required|string|in:active,idle,offline',
        ]);

        $statusMap = [
            'active' => 'approved',
            'idle' => 'pending',
            'offline' => 'rejected',
        ];

        $registration->update([
            'first_name' => $validated['firstName'],
            'middle_name' => $validated['middleName'] ?? '',
            'last_name' => $validated['lastName'],
            'phone' => $validated['phone'],
            'full_phone' => $validated['phone'],
            'vehicle_type' => $validated['vehicleType'],
            'vehicle_model' => $validated['vehicleModel'] ?? '',
            'vehicle_color' => $validated['vehicleColor'] ?? '',
            'plate_number' => $validated['plate'],
            'status' => $statusMap[$validated['status']] ?? 'approved',
        ]);

        return response()->json([
            'message' => 'Driver updated successfully.',
            'driver' => $this->formatForAdmin($registration->fresh()),
        ]);
    }

    public function profile(Request $request)
    {
        $driver = $request->user();

        return response()->json([
            'name' => trim(implode(' ', array_filter([$driver->first_name, $driver->middle_name, $driver->last_name]))),
            'email' => $driver->email,
            'phone' => $driver->full_phone,
            'avatar' => $driver->selfie_photo,
            'vehicleType' => $driver->vehicle_type,
            'vehicleModel' => $driver->vehicle_model,
            'vehicleColor' => $driver->vehicle_color,
            'plateNumber' => $driver->plate_number,
            'totalTrips' => 0,
            'rating' => 0,
            'joinedDays' => 0,
        ]);
    }

    public function settings(Request $request)
    {
        $driver = $request->user();

        return response()->json([
            'name' => trim(implode(' ', array_filter([$driver->first_name, $driver->middle_name, $driver->last_name]))),
            'driver_id' => $this->formatId($driver),
            'auto_accept' => (bool) $driver->auto_accept,
            'bulky_enabled' => (bool) $driver->bulky_enabled,
            'express_enabled' => (bool) $driver->express_enabled,
            'preferred_locations' => $driver->preferred_locations ?? [],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'auto_accept' => 'sometimes|boolean',
            'bulky_enabled' => 'sometimes|boolean',
            'express_enabled' => 'sometimes|boolean',
            'preferred_locations' => 'sometimes|array',
            'preferred_locations.*' => 'string',
        ]);

        $driver = $request->user();
        $driver->update($validated);

        return response()->json([
            'message' => 'Settings updated successfully.',
            'auto_accept' => (bool) $driver->auto_accept,
            'bulky_enabled' => (bool) $driver->bulky_enabled,
            'express_enabled' => (bool) $driver->express_enabled,
            'preferred_locations' => $driver->preferred_locations ?? [],
        ]);
    }

    public function recentDeliveries(Request $request)
    {
        $driver = $request->user();

        $orders = Order::where('driver_registration_id', $driver->id)
            ->where('status', 'completed')
            ->with('seller')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                $createdAt = $order->created_at;
                $diffInMinutes = $createdAt ? $createdAt->diffInMinutes(now()) : 0;

                if ($diffInMinutes < 1) {
                    $time = 'Just now';
                } elseif ($diffInMinutes < 60) {
                    $time = $diffInMinutes . ' min' . ($diffInMinutes > 1 ? 's' : '') . ' ago';
                } elseif ($diffInMinutes < 1440) {
                    $hours = (int) floor($diffInMinutes / 60);
                    $time = $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
                } else {
                    $days = (int) floor($diffInMinutes / 1440);
                    $time = $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
                }

                return [
                    'id' => (string) $order->id,
                    'customerName' => $order->customer_name,
                    'address' => $order->delivery_address,
                    'amount' => $order->total,
                    'time' => $time,
                    'status' => $order->status,
                    'tipped' => false,
                ];
            });

        return response()->json([
            'deliveries' => $orders,
        ]);
    }

    public function destroy($id)
    {
        $registration = DriverRegistration::findOrFail($this->resolveId($id));
        $registration->delete();

        return response()->json([
            'message' => 'Driver deleted successfully.',
        ]);
    }

    private function syncDocumentRecord(string $email, string $photoType, string $photoPath): void
    {
        $docTypeMap = [
            'license_front_photo' => 'drivers_license',
            'license_back_photo' => 'drivers_license_back',
            'vehicle_reg_photo' => 'vehicle_registration',
            'nbi_clearance_photo' => 'nbi_clearance',
            'birth_cert_photo' => 'birth_certificate',
            'selfie_photo' => 'selfie_photo',
        ];

        $docType = $docTypeMap[$photoType] ?? null;
        if (!$docType) return;

        $docNames = [
            'drivers_license' => "Driver's License (Front)",
            'drivers_license_back' => "Driver's License (Back)",
            'vehicle_registration' => 'Vehicle Registration (OR/CR)',
            'nbi_clearance' => 'NBI or Police Clearance',
            'birth_certificate' => 'Birth Certificate',
            'selfie_photo' => 'Selfie Photo',
        ];

        DriverDocument::updateOrCreate(
            ['driver_email' => $email, 'type' => $docType],
            [
                'name' => $docNames[$docType] ?? $docType,
                'file_path' => $photoPath,
                'status' => 'pending',
            ]
        );
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

        $filename = 'driver-photos/' . uniqid() . '-' . $fieldName . '.' . $extension;
        Storage::disk('public')->put($filename, $decoded);

        return Storage::disk('public')->url($filename);
    }

    private function resolveId($id): int
    {
        if (is_numeric($id)) {
            return (int) $id;
        }
        if (str_starts_with($id, 'DRIVER-')) {
            return hexdec(substr($id, 7));
        }
        return (int) $id;
    }

    private function formatId($r): string
    {
        return 'DRIVER-' . strtoupper(dechex($r->id));
    }

    private function formatForAdmin($r)
    {
        return [
            'id' => $this->formatId($r),
            'role' => 'driver',
            'firstName' => $r->first_name,
            'middleName' => $r->middle_name,
            'lastName' => $r->last_name,
            'email' => $r->email,
            'contact' => $r->full_phone,
            'licenseNumber' => $r->license_number,
            'licenseType' => $r->license_type,
            'restrictionCode' => $r->restriction_code,
            'expiryDate' => $r->expiry_date,
            'vehicleType' => $r->vehicle_type,
            'plateNumber' => $r->plate_number,
            'vehicleModel' => $r->vehicle_model,
            'vehicleColor' => $r->vehicle_color,
            'country' => $r->country,
            'birthdate' => $r->birthdate,
            'birthplace' => $r->birthplace,
            'currentAddress' => $r->current_address,
            'licenseFrontPhoto' => $r->license_front_photo,
            'licenseBackPhoto' => $r->license_back_photo,
            'vehicleRegPhoto' => $r->vehicle_reg_photo,
            'nbiClearancePhoto' => $r->nbi_clearance_photo,
            'birthCertPhoto' => $r->birth_cert_photo,
            'selfiePhoto' => $r->selfie_photo,
            'status' => $r->status,
            'submittedAt' => $r->created_at->toDateString(),
            'reviewedAt' => $r->reviewed_at?->toDateString(),
            'reviewedBy' => $r->reviewedBy?->name ?? null,
            'rejectReason' => $r->rejection_reason,
        ];
    }
}
