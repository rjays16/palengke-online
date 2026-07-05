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
            ['type' => 'drivers_license', 'name' => "Driver's License"],
            ['type' => 'vehicle_registration', 'name' => 'Vehicle Registration (OR/CR)'],
            ['type' => 'nbi_clearance', 'name' => 'NBI or Police Clearance'],
            ['type' => 'birth_certificate', 'name' => 'Birth Certificate'],
            ['type' => 'selfie_photo', 'name' => 'Selfie Photo'],
            ['type' => 'official_receipt', 'name' => 'Official Receipt (OR)'],
            ['type' => 'business_permit', 'name' => 'Business or Delivery Permit'],
            ['type' => 'identity_card', 'name' => 'Identity Card'],
        ];
    }
}
