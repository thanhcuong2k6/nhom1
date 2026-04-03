<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::all();
        $newProducts = Product::where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        return view('home.index', compact('featuredProducts', 'categories', 'newProducts'));
    }

    public function search()
    {
        $query = request('q');

        if (!$query || strlen($query) < 2) {
            return redirect('/');
        }

        $products = Product::where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->paginate(12);

        return view('home.search', compact('products', 'query'));
    }
}
