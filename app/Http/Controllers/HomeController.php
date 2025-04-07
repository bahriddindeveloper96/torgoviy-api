<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // Get main categories with translations
            $categories = Category::with('translations')
                ->whereNull('parent_id')
                ->take(8)
                ->get();

            // Get latest products with images and attributes
            $newProducts = Product::with(['images', 'attributes'])
                ->latest()
                ->take(8)
                ->get();

            // Get top products (based on views/popularity)
            $topProducts = Product::with(['images', 'attributes'])
                ->orderBy('views', 'desc')
                ->take(8)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => $categories,
                    'new_products' => ProductResource::collection($newProducts),
                    'top_products' => ProductResource::collection($topProducts)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
