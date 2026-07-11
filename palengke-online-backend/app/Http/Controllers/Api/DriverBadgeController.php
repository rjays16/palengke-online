<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\DriverRegistration;
use Illuminate\Http\Request;

class DriverBadgeController extends Controller
{
    public function index(Request $request)
    {
        $driver = $request->user();
        $completedTrips = $driver->orders()->where('status', 'completed')->count();

        $allBadges = Badge::orderBy('criteria_value')->get();
        $earnedBadgeIds = $driver->badges()->pluck('badge_id')->toArray();

        $newlyEarned = [];

        foreach ($allBadges as $badge) {
            if ($completedTrips >= $badge->criteria_value && !in_array($badge->id, $earnedBadgeIds)) {
                $driver->badges()->attach($badge->id, [
                    'is_new' => true,
                    'awarded_at' => now(),
                ]);
                $newlyEarned[] = $badge->id;
                $earnedBadgeIds[] = $badge->id;
            }
        }

        $badges = $allBadges->map(function ($badge) use ($driver, $completedTrips, $earnedBadgeIds) {
            $isEarned = in_array($badge->id, $earnedBadgeIds);
            $pivot = $isEarned ? $driver->badges()->where('badge_id', $badge->id)->first()?->pivot : null;

            return [
                'id' => $badge->id,
                'name' => $badge->name,
                'description' => $badge->description,
                'icon' => $badge->icon_name,
                'color' => $badge->color,
                'isNew' => $pivot?->is_new ?? false,
                'earned' => $isEarned,
                'progress' => min(100, ($completedTrips / $badge->criteria_value) * 100),
                'progressText' => $isEarned ? 'Earned' : "{$completedTrips} / {$badge->criteria_value} trips",
            ];
        });

        return response()->json([
            'badges' => $badges,
            'newlyEarned' => $newlyEarned,
        ]);
    }

    public function markRead(Request $request)
    {
        $driver = $request->user();
        $driver->badges()->newPivotStatement()
            ->where('driver_registration_id', $driver->id)
            ->where('is_new', true)
            ->update(['is_new' => false]);

        return response()->json(['message' => 'Badges marked as read.']);
    }
}
