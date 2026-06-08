<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::orderBy('sort_order')->get()->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'icon' => $cat->icon,
                'icon_url' => $cat->icon_url,
                'color' => $cat->color,
                'sort_order' => $cat->sort_order,
            ];
        }));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'icon' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        if ($request->hasFile('icon')) {
            $validated['icon'] = $request->file('icon')->store('categories', 'public');
        }

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully.',
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'icon' => $category->icon,
                'icon_url' => $category->icon_url,
                'color' => $category->color,
                'sort_order' => $category->sort_order,
            ],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer',
            'icon' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        if ($request->hasFile('icon')) {
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }
            $validated['icon'] = $request->file('icon')->store('categories', 'public');
        }

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully.',
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'icon' => $category->icon,
                'icon_url' => $category->icon_url,
                'color' => $category->color,
                'sort_order' => $category->sort_order,
            ],
        ]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if ($category->icon) {
            Storage::disk('public')->delete($category->icon);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully.']);
    }
}
