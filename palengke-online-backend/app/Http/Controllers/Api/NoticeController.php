<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        return response()->json([
            'notices' => Notice::orderBy('created_at', 'desc')->get()->map(fn($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'target' => $n->target,
                'active' => $n->active,
                'createdAt' => $n->created_at->format('Y-m-d'),
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target' => 'required|string|in:all,customer,seller,driver',
            'active' => 'boolean',
        ]);

        $notice = Notice::create($validated);

        return response()->json([
            'message' => 'Notice created.',
            'notice' => [
                'id' => $notice->id,
                'title' => $notice->title,
                'message' => $notice->message,
                'target' => $notice->target,
                'active' => $notice->active,
                'createdAt' => $notice->created_at->format('Y-m-d'),
            ],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $notice = Notice::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target' => 'required|string|in:all,customer,seller,driver',
            'active' => 'boolean',
        ]);

        $notice->update($validated);

        return response()->json([
            'message' => 'Notice updated.',
            'notice' => [
                'id' => $notice->id,
                'title' => $notice->title,
                'message' => $notice->message,
                'target' => $notice->target,
                'active' => $notice->active,
                'createdAt' => $notice->created_at->format('Y-m-d'),
            ],
        ]);
    }

    public function destroy($id)
    {
        Notice::findOrFail($id)->delete();

        return response()->json(['message' => 'Notice deleted.']);
    }

    public function active(Request $request)
    {
        $target = $request->query('target', 'all');

        $notices = Notice::where('active', true)
            ->where(function ($q) use ($target) {
                $q->where('target', $target)->orWhere('target', 'all');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'target' => $n->target,
                'active' => $n->active,
                'createdAt' => $n->created_at->format('Y-m-d'),
            ]);

        return response()->json(['notices' => $notices]);
    }
}
