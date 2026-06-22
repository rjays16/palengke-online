<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HelpTopic;
use Illuminate\Http\Request;

class HelpTopicController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role', 'driver');

        $topics = HelpTopic::orderBy('sort_order')
            ->where('enabled', true)
            ->where('role', $role)
            ->get();

        return response()->json([
            'topics' => $topics,
        ]);
    }

    public function adminIndex()
    {
        $topics = HelpTopic::orderBy('role')->orderBy('sort_order')->get();

        return response()->json([
            'topics' => $topics,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'topics' => 'required|array',
            'topics.*.id' => 'required|integer|exists:help_topics,id',
            'topics.*.enabled' => 'required|boolean',
        ]);

        foreach ($validated['topics'] as $item) {
            HelpTopic::where('id', $item['id'])->update(['enabled' => $item['enabled']]);
        }

        return response()->json([
            'message' => 'Help topic settings updated successfully.',
        ]);
    }
}
