<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'submitted_by' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'sometimes|string|in:low,medium,high',
        ]);

        $report = Report::create([
            'type' => $validated['type'],
            'submitted_by' => $validated['submitted_by'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'] ?? 'medium',
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Report submitted successfully.',
            'report' => $report,
        ], 201);
    }

    public function myReports(Request $request)
    {
        $request->validate(['email' => 'required|string']);

        $reports = Report::where('submitted_by', $request->email)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['reports' => $reports]);
    }

    public function adminIndex()
    {
        $reports = Report::orderBy('created_at', 'desc')->get();

        return response()->json([
            'reports' => $reports,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,in_progress,resolved,dismissed',
        ]);

        $report = Report::findOrFail($id);
        $report->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Report status updated.',
            'report' => $report,
        ]);
    }
}
