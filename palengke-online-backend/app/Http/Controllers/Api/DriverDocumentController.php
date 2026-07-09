<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DriverDocument;
use App\Models\DriverRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DriverDocumentController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate(['email' => 'required|email']);

        $registration = DriverRegistration::where('email', $validated['email'])->first();
        if (!$registration) {
            return response()->json(['documents' => []]);
        }

        $photoMap = [
            'drivers_license' => 'license_front_photo',
            'drivers_license_back' => 'license_back_photo',
            'vehicle_registration' => 'vehicle_reg_photo',
            'nbi_clearance' => 'nbi_clearance_photo',
            'birth_certificate' => 'birth_cert_photo',
            'selfie_photo' => 'selfie_photo',
        ];

        $docTypes = collect($this->getDocumentTypes());
        $validTypes = $docTypes->pluck('type');

        $existing = DriverDocument::where('driver_email', $validated['email'])->get();
        $existingTypes = $existing->pluck('type');

        DriverDocument::where('driver_email', $validated['email'])
            ->whereNotIn('type', $validTypes)
            ->delete();

        foreach ($docTypes as $typeInfo) {
            $type = $typeInfo['type'];
            if (!$existingTypes->contains($type)) {
                $dbField = $photoMap[$type] ?? null;
                $hasPhoto = $dbField && $registration->{$dbField};
                DriverDocument::create([
                    'driver_email' => $validated['email'],
                    'type' => $type,
                    'name' => $typeInfo['name'],
                    'file_path' => $hasPhoto ? $registration->{$dbField} : null,
                    'status' => $hasPhoto ? 'verified' : 'missing',
                ]);
            }
        }

        $documents = DriverDocument::where('driver_email', $validated['email'])->get();

        foreach ($documents as $doc) {
            $dbField = $photoMap[$doc->type] ?? null;
            if ($doc->status === 'pending' && $dbField && $registration->{$dbField}) {
                $doc->update(['status' => 'verified', 'file_path' => $registration->{$dbField}]);
            }
        }

        $documents = DriverDocument::where('driver_email', $validated['email'])->get();

        $docToRegMap = [
            'drivers_license' => 'license_front_photo',
            'drivers_license_back' => 'license_back_photo',
            'vehicle_registration' => 'vehicle_reg_photo',
            'nbi_clearance' => 'nbi_clearance_photo',
            'birth_certificate' => 'birth_cert_photo',
            'selfie_photo' => 'selfie_photo',
        ];

        foreach ($documents as $doc) {
            if ($doc->file_path) {
                $regField = $docToRegMap[$doc->type] ?? null;
                if ($regField && !$registration->{$regField}) {
                    $registration->update([$regField => $doc->file_path]);
                }
            }
        }

        $documents = DriverDocument::where('driver_email', $validated['email'])->get();

        return response()->json([
            'documents' => $documents->map(fn($doc) => $this->formatDocument($doc)),
        ]);
    }

    public function show($id)
    {
        $document = DriverDocument::findOrFail($id);
        return response()->json([
            'document' => $this->formatDocument($document),
        ]);
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'type' => 'required|string',
            'photo' => 'required|string',
        ]);

        $registration = DriverRegistration::where('email', $validated['email'])->first();
        if (!$registration) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        $documentTypes = $this->getDocumentTypes();
        $typeInfo = collect($documentTypes)->firstWhere('type', $validated['type']);

        if (!$typeInfo) {
            return response()->json(['message' => 'Invalid document type.'], 422);
        }

        $photoPath = $this->saveBase64Image($validated['photo'], $validated['type']);

        $document = DriverDocument::updateOrCreate(
            [
                'driver_email' => $validated['email'],
                'type' => $validated['type'],
            ],
            [
                'name' => $typeInfo['name'],
                'file_path' => $photoPath,
                'status' => 'pending',
            ]
        );

        $docToRegMap = [
            'drivers_license' => 'license_front_photo',
            'drivers_license_back' => 'license_back_photo',
            'vehicle_registration' => 'vehicle_reg_photo',
            'nbi_clearance' => 'nbi_clearance_photo',
            'birth_certificate' => 'birth_cert_photo',
            'selfie_photo' => 'selfie_photo',
        ];

        $regField = $docToRegMap[$validated['type']] ?? null;
        if ($regField && !$registration->{$regField}) {
            $registration->update([$regField => $photoPath]);
        }

        return response()->json([
            'message' => 'Document uploaded successfully.',
            'document' => $this->formatDocument($document),
        ]);
    }

    public function types()
    {
        return response()->json([
            'types' => $this->getDocumentTypes(),
        ]);
    }

    public function seed(Request $request)
    {
        $validated = $request->validate(['email' => 'required|email']);

        $registration = DriverRegistration::where('email', $validated['email'])->first();
        if (!$registration) {
            return response()->json(['message' => 'Driver not found.'], 404);
        }

        $types = $this->getDocumentTypes();

        foreach ($types as $typeInfo) {
            DriverDocument::firstOrCreate(
                [
                    'driver_email' => $validated['email'],
                    'type' => $typeInfo['type'],
                ],
                [
                    'name' => $typeInfo['name'],
                    'file_path' => null,
                    'expiry_date' => null,
                    'status' => 'missing',
                ]
            );
        }

        $this->migrateExistingPhotos($registration);

        $documents = DriverDocument::where('driver_email', $validated['email'])->get();

        return response()->json([
            'message' => 'Documents seeded successfully.',
            'documents' => $documents->map(fn($doc) => $this->formatDocument($doc)),
        ]);
    }

    private function migrateExistingPhotos(DriverRegistration $registration)
    {
        $photoMap = [
            'license_front_photo' => 'drivers_license',
            'license_back_photo' => 'drivers_license_back',
            'vehicle_reg_photo' => 'vehicle_registration',
            'nbi_clearance_photo' => 'nbi_clearance',
            'birth_cert_photo' => 'birth_certificate',
            'selfie_photo' => 'selfie_photo',
        ];

        foreach ($photoMap as $dbField => $docType) {
            $photoPath = $registration->{$dbField};
            if (!$photoPath) continue;

            $docTypeInfo = collect($this->getDocumentTypes())->firstWhere('type', $docType);
            if (!$docTypeInfo) continue;

            DriverDocument::updateOrCreate(
                [
                    'driver_email' => $registration->email,
                    'type' => $docType,
                ],
                [
                    'name' => $docTypeInfo['name'],
                    'file_path' => $photoPath,
                    'status' => 'verified',
                ]
            );
        }
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

        $filename = 'driver-documents/' . uniqid() . '-' . $fieldName . '.' . $extension;
        Storage::disk('public')->put($filename, $decoded);

        return Storage::disk('public')->url($filename);
    }

    private function formatDocument($doc): array
    {
        $fileExists = $doc->file_path && !str_contains($doc->file_path, 'null');

        $status = $doc->status;
        if (!$fileExists) {
            $status = 'missing';
        }

        return [
            'id' => $doc->id,
            'name' => $doc->name,
            'type' => $doc->type,
            'photo_url' => $fileExists ? $doc->file_path : null,
            'expiry' => $doc->expiry_date,
            'status' => $status,
        ];
    }

    private function getDocumentTypes(): array
    {
        return [
            ['type' => 'drivers_license', 'name' => "Driver's License (Front)"],
            ['type' => 'drivers_license_back', 'name' => "Driver's License (Back)"],
            ['type' => 'vehicle_registration', 'name' => 'Vehicle Registration (OR/CR)'],
            ['type' => 'nbi_clearance', 'name' => 'NBI or Police Clearance'],
            ['type' => 'birth_certificate', 'name' => 'Birth Certificate'],
            ['type' => 'selfie_photo', 'name' => 'Selfie Photo'],
        ];
    }
}
