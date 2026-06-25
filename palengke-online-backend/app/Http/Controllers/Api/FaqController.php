<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        return response()->json([
            'faqs' => Faq::orderBy('sort_order')->orderBy('created_at', 'desc')->get()->map(fn($f) => [
                'id' => $f->id,
                'question' => $f->question,
                'answer' => $f->answer,
                'role' => $f->role,
                'topic' => $f->topic,
                'active' => $f->active,
                'sort_order' => $f->sort_order,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'role' => 'nullable|string|in:driver,seller',
            'topic' => 'nullable|string|max:50',
            'active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $faq = Faq::create($validated);

        return response()->json([
            'message' => 'FAQ created.',
            'faq' => [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'role' => $faq->role,
                'topic' => $faq->topic,
                'active' => $faq->active,
                'sort_order' => $faq->sort_order,
            ],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'role' => 'nullable|string|in:driver,seller',
            'topic' => 'nullable|string|max:50',
            'active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $faq->update($validated);

        return response()->json([
            'message' => 'FAQ updated.',
            'faq' => [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'role' => $faq->role,
                'topic' => $faq->topic,
                'active' => $faq->active,
                'sort_order' => $faq->sort_order,
            ],
        ]);
    }

    public function destroy($id)
    {
        Faq::findOrFail($id)->delete();

        return response()->json(['message' => 'FAQ deleted.']);
    }

    public function active(Request $request)
    {
        $query = Faq::where('active', true);

        $role = $request->query('role');
        if ($role && in_array($role, ['driver', 'seller'])) {
            $query->where(function ($q) use ($role) {
                $q->where('role', $role)->orWhereNull('role');
            });
        }

        $faqs = $query->orderBy('sort_order')->orderBy('created_at', 'desc')->get()->map(fn($f) => [
            'id' => $f->id,
            'question' => $f->question,
            'answer' => $f->answer,
            'topic' => $f->topic,
        ]);

        return response()->json(['faqs' => $faqs]);
    }
}
