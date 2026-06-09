<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $products = Product::where('seller_registration_id', $seller->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($p) => $this->formatProduct($p));

        return response()->json(['products' => $products]);
    }

    public function show(Request $request, $id)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $product = Product::where('seller_registration_id', $seller->id)->where('id', $id)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return response()->json(['product' => $this->formatProduct($product)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'selling_unit' => 'required|string|in:kilo,piece,pack',
            'price' => 'required|numeric|min:0',
            'minimum_order' => 'nullable|numeric|min:0',
            'has_discount' => 'nullable|boolean',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'status' => 'nullable|string|in:active,draft',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        unset($validated['email']);

        $product = Product::create(array_merge($validated, [
            'seller_registration_id' => $seller->id,
        ]));

        return response()->json([
            'message' => 'Product added successfully.',
            'product' => $this->formatProduct($product),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'selling_unit' => 'sometimes|string|in:kilo,piece,pack',
            'price' => 'sometimes|numeric|min:0',
            'minimum_order' => 'nullable|numeric|min:0',
            'has_discount' => 'nullable|boolean',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'sometimes|numeric|min:0',
            'sku' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'status' => 'nullable|string|in:active,draft',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $product = Product::where('seller_registration_id', $seller->id)->where('id', $id)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        unset($validated['email']);
        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully.',
            'product' => $this->formatProduct($product),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();

        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $product = Product::where('seller_registration_id', $seller->id)->where('id', $id)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }

    private function formatProduct($p)
    {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'category' => $p->category,
            'description' => $p->description,
            'selling_unit' => $p->selling_unit,
            'price' => (float) $p->price,
            'minimum_order' => $p->minimum_order ? (float) $p->minimum_order : null,
            'has_discount' => $p->has_discount,
            'discount_price' => $p->discount_price ? (float) $p->discount_price : null,
            'stock_quantity' => (float) $p->stock_quantity,
            'sku' => $p->sku,
            'origin' => $p->origin,
            'brand' => $p->brand,
            'is_featured' => $p->is_featured,
            'is_active' => $p->is_active,
            'images' => $p->images ?? [],
            'status' => $p->status,
            'created_at' => $p->created_at,
        ];
    }
}
