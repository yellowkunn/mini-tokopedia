<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Product;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //

    public function show(Category $category)
    {
        // Kita ambil produk yang hanya termasuk dalam kategori ini
        $products = Product::where('category_id', $category->id)->paginate(12);

        // Tampilkan ke halaman khusus kategori
        return view('category.show', compact('category', 'products'));
    }
}
