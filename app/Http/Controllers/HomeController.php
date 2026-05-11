<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $flashSaleProducts = Product::whereNotNull('original_price')
            ->whereColumn('price', '<', 'original_price')
            ->limit(6)
            ->get();

        $recommendedProducts = Product::latest()->paginate(12);
        $categories = Category::oldest()->get();
        return view('home', compact('flashSaleProducts', 'recommendedProducts', 'categories'));
    }
}
