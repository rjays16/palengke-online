<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'submitted_by' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'sometimes|string|in:feedback,feature_request',
        ]);

        $feedback = Feedback::create([
            'type' => $validated['type'] ?? 'feedback',
            'submitted_by' => $validated['submitted_by'],
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Feedback submitted successfully.',
            'feedback' => $feedback,
        ], 201);
    }

    public function adminIndex()
    {
        $feedbacks = Feedback::orderBy('created_at', 'desc')->get();

        return response()->json([
            'feedbacks' => $feedbacks,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,reviewed,archived',
        ]);

        $feedback = Feedback::findOrFail($id);
        $feedback->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Feedback status updated.',
            'feedback' => $feedback,
        ]);
    }
}
