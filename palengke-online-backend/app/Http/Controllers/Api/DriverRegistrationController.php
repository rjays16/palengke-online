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
use Illuminate\Support\Facades\DB;

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

        $totalTrips = $driver->orders()->where('status', 'completed')->count();
        $joinedDays = $driver->created_at ? (int) $driver->created_at->diffInDays(now()) : 0;

        $ratings = $driver->ratings()->get();
        $rating = $ratings->isNotEmpty() ? round($ratings->avg('rating'), 1) : 0;

        $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($ratings as $r) {
            if (isset($ratingCounts[$r->rating])) {
                $ratingCounts[$r->rating]++;
            }
        }

        $cityAvg = $this->cityAverageRating($driver);

        return response()->json([
            'name' => trim(implode(' ', array_filter([$driver->first_name, $driver->middle_name, $driver->last_name]))),
            'email' => $driver->email,
            'phone' => $driver->full_phone,
            'avatar' => $driver->selfie_photo,
            'vehicleType' => $driver->vehicle_type,
            'vehicleModel' => $driver->vehicle_model,
            'vehicleColor' => $driver->vehicle_color,
            'plateNumber' => $driver->plate_number,
            'totalTrips' => $totalTrips,
            'rating' => $rating,
            'joinedDays' => $joinedDays,
            'ratingCounts' => $ratingCounts,
            'cityAvgRating' => $cityAvg,
            'ratingUpdatedAt' => $ratings->isNotEmpty() ? $ratings->max('created_at')->toIso8601String() : null,
            'availabilityStatus' => $driver->availability_status,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:active,offline',
        ]);

        $driver = $request->user();
        $driver->update(['availability_status' => $validated['status']]);

        return response()->json([
            'message' => 'Availability status updated successfully.',
            'availabilityStatus' => $driver->availability_status,
        ]);
    }

    private function cityAverageRating($driver)
    {
        $preferred = is_array($driver->preferred_locations) ? $driver->preferred_locations : [];
        $query = \App\Models\DriverRating::query()
            ->where('driver_registration_id', '!=', $driver->id)
            ->whereNotNull('rating');

        if (!empty($preferred)) {
            $query->whereHas('driver', function ($q) use ($preferred) {
                $q->where(function ($q2) use ($preferred) {
                    foreach ($preferred as $location) {
                        $q2->orWhereJsonContains('preferred_locations', $location);
                    }
                });
            });
        }

        $avg = $query->avg('rating');
        return $avg !== null ? round($avg, 1) : 0;
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
                    'tipped' => (bool) ($order->tip_amount > 0),
                    'tipAmount' => (float) ($order->tip_amount ?? 0),
                ];
            });

        return response()->json([
            'deliveries' => $orders,
        ]);
    }

    public function orders(Request $request)
    {
        $driver = $request->user();

        $unassigned = Order::whereNull('driver_registration_id')
            ->where('status', 'pending')
            ->with('seller')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) use ($driver) {
                return $this->formatOrderForDriver($order, 'available');
            });

        $active = Order::where('driver_registration_id', $driver->id)
            ->whereIn('status', ['assigned', 'processing'])
            ->with('seller')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($o) => $this->formatOrderForDriver($o, 'active'));

        $completed = Order::where('driver_registration_id', $driver->id)
            ->where('status', 'completed')
            ->with('seller')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($o) => $this->formatOrderForDriver($o, 'completed'));

        return response()->json([
            'available' => $unassigned,
            'active' => $active,
            'completed' => $completed,
        ]);
    }

    private function formatOrderForDriver($order, $driverStatus)
    {
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

        $itemCount = count($order->items ?? []);
        $total = (float) $order->total;
        $driverEarnings = round($total * 0.10, 2);

        return [
            'id' => (string) $order->id,
            'status' => $driverStatus,
            'dbStatus' => $order->status,
            'sellerName' => $order->seller?->store_name ?? $order->seller?->first_name ?? 'Store',
            'pickupAddress' => $order->seller?->address ?? $order->seller?->city ?? 'Pickup Location',
            'customerName' => $order->customer_name,
            'deliveryAddress' => $order->delivery_address,
            'distance' => 0,
            'estimatedTime' => max(10, $itemCount * 3),
            'itemCount' => $itemCount,
            'totalAmount' => $total,
            'driverEarnings' => $driverEarnings,
            'paymentMethod' => $order->payment_method ?? 'cash',
            'pickupPhone' => $order->seller?->full_phone ?? $order->seller?->phone ?? null,
            'customerPhone' => $order->customer_phone,
            'pickupInstructions' => $order->seller?->store_place ?? null,
            'items' => $order->items ?? [],
            'latitude' => $order->latitude !== null ? (float) $order->latitude : null,
            'longitude' => $order->longitude !== null ? (float) $order->longitude : null,
            'sellerLatitude' => $order->seller?->latitude !== null ? (float) $order->seller->latitude : null,
            'sellerLongitude' => $order->seller?->longitude !== null ? (float) $order->seller->longitude : null,
            'time' => $time,
            'tipped' => (bool) ($order->tip_amount > 0),
            'tipAmount' => (float) ($order->tip_amount ?? 0),
            'completedTime' => $driverStatus === 'completed' ? $time : null,
        ];
    }

    public function driverHistory(Request $request)
    {
        $driver = $request->user();

        $orders = Order::where('driver_registration_id', $driver->id)
            ->where('status', 'completed')
            ->with('seller')
            ->orderBy('created_at', 'desc')
            ->get();

        $dailyEarnings = [];
        $jobsByDate = [];

        foreach ($orders as $order) {
            $date = $order->created_at->format('Y-m-d');
            $dayOfMonth = (int) $order->created_at->format('j');
            $time = $order->created_at->format('g:i A');

            if (!isset($dailyEarnings[$date])) {
                $dailyEarnings[$date] = 0;
            }
            $dailyEarnings[$date] += $order->total;

            if (!isset($jobsByDate[$date])) {
                $jobsByDate[$date] = [];
            }

            $jobsByDate[$date][] = [
                'id' => (string) $order->id,
                'time' => $time,
                'sellerName' => $order->seller->store_name ?? $order->seller->first_name ?? 'Store',
                'deliveryAddress' => $order->delivery_address,
                'amount' => $order->total,
                'paymentMethod' => $order->payment_method ?? 'cash',
                'tipped' => (bool) ($order->tip_amount > 0),
                'tipAmount' => (float) ($order->tip_amount ?? 0),
                'bonus' => 0,
                'dayOfMonth' => $dayOfMonth,
                'date' => $date,
            ];
        }

        ksort($dailyEarnings);
        ksort($jobsByDate);

        return response()->json([
            'dailyEarnings' => $dailyEarnings,
            'jobsByDate' => $jobsByDate,
        ]);
    }

    public function earnings(Request $request)
    {
        $driver = $request->user();

        $completedOrders = Order::where('driver_registration_id', $driver->id)
            ->where('status', 'completed')
            ->get();

        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $yesterdayStart = $now->copy()->subDay()->startOfDay();
        $yesterdayEnd = $now->copy()->subDay()->endOfDay();
        $weekStart = $now->copy()->startOfWeek();
        $monthStart = $now->copy()->startOfMonth();

        $todayOrders = $completedOrders->filter(fn($o) => $o->created_at >= $todayStart);
        $yesterdayOrders = $completedOrders->filter(fn($o) => $o->created_at >= $yesterdayStart && $o->created_at <= $yesterdayEnd);
        $weekOrders = $completedOrders->filter(fn($o) => $o->created_at >= $weekStart);
        $monthOrders = $completedOrders->filter(fn($o) => $o->created_at >= $monthStart);

        $dailyEarnings = [];
        foreach ($completedOrders as $order) {
            $date = $order->created_at->format('Y-m-d');
            if (!isset($dailyEarnings[$date])) {
                $dailyEarnings[$date] = 0;
            }
            $dailyEarnings[$date] += $order->total;
        }
        ksort($dailyEarnings);

        $buildStats = function ($orders) use ($now) {
            $total = $orders->sum('total');
            $jobs = $orders->count();
            $netEarnings = round($total * 0.90, 2);
            $commission = round($total * 0.10, 2);
            $avgPerJob = $jobs > 0 ? round($netEarnings / $jobs, 2) : 0;
            $tips = round($orders->sum('tip_amount'), 2);

            return [
                'netEarnings' => $netEarnings,
                'jobs' => $jobs,
                'tips' => $tips,
                'onlineHours' => 0,
                'avgPerJob' => $avgPerJob,
                'deliveryFee' => $total,
                'commission' => $commission,
                'completionRate' => 100,
                'rating' => 5.0,
                'acceptRate' => 100,
                'totalKm' => 0,
            ];
        };

        $buildChart = function ($startDate, $endDate) use ($dailyEarnings) {
            $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            $today = now()->toDateString();
            $chart = [];
            $current = $startDate->copy();
            while ($current <= $endDate) {
                $dateStr = $current->format('Y-m-d');
                $chart[] = [
                    'label' => $dayNames[$current->dayOfWeek],
                    'amount' => $dailyEarnings[$dateStr] ?? 0,
                    'isToday' => $dateStr === $today,
                ];
                $current->addDay();
            }
            return $chart;
        };

        return response()->json([
            'today' => $buildStats($todayOrders),
            'yesterday' => $buildStats($yesterdayOrders),
            'week' => $buildStats($weekOrders),
            'month' => $buildStats($monthOrders),
            'chart' => $buildChart($weekStart, $now->copy()->endOfWeek()),
        ]);
    }

    private function driverWallet($driver)
    {
        $codEarnings = (float) Order::where('driver_registration_id', $driver->id)
            ->where('status', 'completed')
            ->where('payment_method', 'cod')
            ->sum('total');

        $cashedOut = (float) $driver->cashOuts()->sum('amount');

        return [
            'codEarnings' => round($codEarnings, 2),
            'cashedOut' => round($cashedOut, 2),
            'balance' => round($codEarnings - $cashedOut, 2),
        ];
    }

    public function walletBalance(Request $request)
    {
        return response()->json($this->driverWallet($request->user()));
    }

    public function cashOut(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $driver = $request->user();
        $wallet = $this->driverWallet($driver);
        $amount = round((float) $validated['amount'], 2);

        if ($amount > $wallet['balance']) {
            return response()->json([
                'message' => 'Amount exceeds your available balance.',
            ], 422);
        }

        $driver->cashOuts()->create(['amount' => $amount]);

        return response()->json([
            'message' => 'Cash out successful.',
            'balance' => round($wallet['balance'] - $amount, 2),
        ]);
    }

    public function acceptOrder(Request $request, $id)
    {
        $driver = $request->user();

        if ($driver->status !== 'approved') {
            return response()->json(['message' => 'Your account is not approved.'], 403);
        }

        $order = DB::transaction(function () use ($id, $driver) {
            $order = Order::whereNull('driver_registration_id')
                ->where('status', 'pending')
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$order) {
                return null;
            }

            $order->update([
                'driver_registration_id' => $driver->id,
                'status' => 'assigned',
            ]);

            return $order;
        });

        if (!$order) {
            return response()->json(['message' => 'Order is no longer available.'], 404);
        }

        Notification::create([
            'type' => 'order_accepted',
            'title' => 'Order Accepted',
            'message' => "You accepted Order #{$order->id}. Head to pickup.",
            'category' => 'orders',
            'driver_registration_id' => $driver->id,
            'data' => ['order_id' => $order->id],
        ]);

        return response()->json([
            'message' => 'Order accepted successfully.',
            'order' => $this->formatOrderForDriver($order->fresh()->load('seller'), 'active'),
        ]);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:picked_up,delivered,cancelled',
        ]);

        $driver = $request->user();

        $order = Order::where('id', $id)
            ->where('driver_registration_id', $driver->id)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        if (!in_array($order->status, ['assigned', 'processing'])) {
            return response()->json(['message' => 'Order cannot be updated in its current state.'], 400);
        }

        $status = $validated['status'];
        $newStatus = match ($status) {
            'picked_up' => 'processing',
            'delivered' => 'completed',
            'cancelled' => 'cancelled',
        };

        $order->update(['status' => $newStatus]);

        if ($order->seller) {
            Notification::create([
                'seller_registration_id' => $order->seller->id,
                'type' => 'order_update',
                'title' => $status === 'delivered' ? 'Order Delivered' : ($status === 'cancelled' ? 'Order Cancelled' : 'Order Picked Up'),
                'category' => 'orders',
                'message' => $status === 'delivered'
                    ? "Your order #{$order->id} has been delivered."
                    : ($status === 'cancelled'
                        ? "Order #{$order->id} was cancelled by the driver."
                        : "Order #{$order->id} has been picked up by the driver."),
                'data' => ['order_id' => $order->id, 'status' => $newStatus],
            ]);
        }

        if ($status === 'delivered' && (float) ($order->tip_amount ?? 0) > 0) {
            $customerName = $order->tip_customer_name ?? $order->customer_name ?? 'Customer';
            Notification::create([
                'driver_registration_id' => $driver->id,
                'type' => 'tip',
                'title' => 'You Got a Tip!',
                'category' => 'earnings',
                'message' => $customerName . ' tipped you ₱' . number_format((float) $order->tip_amount, 2) . '.',
                'data' => [
                    'order_id' => $order->id,
                    'amount' => (float) $order->tip_amount,
                    'customer_name' => $customerName,
                    'message' => $order->tip_message,
                    'delivery_fee' => (float) ($order->delivery_fee ?? 0),
                ],
            ]);
        }

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
            ],
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
