<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['store', 'category']);

        // Filter Kategori
        if ($request->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        // Sorting (Urutan)
        if ($request->sort) {
            match ($request->sort) {
                'price_asc' => $query->orderBy('price', 'asc'),
                'price_desc' => $query->orderBy('price', 'desc'),
                'newest' => $query->orderBy('created_at', 'desc'),
                default => $query->latest(),
            };
        }

        return view('products.index', [
            'products' => $query->paginate(24)->appends(request()->query()), //otomatis membawa filter yang telah digunakan pada page sebelumnya
            'categories' => Category::oldest()->get(),
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['store', 'category']);

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
