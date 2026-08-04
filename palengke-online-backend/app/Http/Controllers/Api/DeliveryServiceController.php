<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryService;
use App\Models\SystemSetting;

class DeliveryServiceController extends Controller
{
    public function index()
    {
        $expressGloballyEnabled = SystemSetting::where('key', 'express_delivery_enabled')->value('value') === 'true';

        return response()->json(
            DeliveryService::orderBy('sort_order')
                ->get()
                ->map(function ($service) use ($expressGloballyEnabled) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'slug' => $service->slug,
                        'description' => $service->description,
                        'icon' => $service->icon,
                        'color' => $service->color,
                        'tag' => $service->tag,
                        'tag_type' => $service->tag_type,
                        'always_on' => (bool) $service->always_on,
                        'driver_setting' => $service->driver_setting,
                        'global_enabled' => $service->slug === 'express' ? $expressGloballyEnabled : true,
                    ];
                })
        );
    }
}
