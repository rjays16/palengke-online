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

    public function active()
    {
        $faqs = Faq::where('active', true)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($f) => [
                'id' => $f->id,
                'question' => $f->question,
                'answer' => $f->answer,
            ]);

        return response()->json(['faqs' => $faqs]);
    }
}
