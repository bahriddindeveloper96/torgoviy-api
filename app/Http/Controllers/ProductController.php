<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'user']);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
        }

        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->condition) {
            $query->where('condition', $request->condition);
        }

        if ($request->location) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        $products = $query->latest()->paginate(12);
        return response()->json($products);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'user']);
        return response()->json($product);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|in:new,used',
            'location' => 'required|string|max:255',
            'images.*' => 'required|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['images'] = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $data['images'][] = $path;
            }
        }

        $product = Product::create($data);

        return response()->json($product, 201);
    }

    public function update(Request $request, Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|in:new,used',
            'location' => 'required|string|max:255',
            'images.*' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->all();

        if ($request->hasFile('images')) {
            $data['images'] = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $data['images'][] = $path;
            }
        }

        $product->update($data);

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $product->delete();
        return response()->json(null, 204);
    }

    public function toggleFavorite(Product $product)
    {
        $user = Auth::user();
        
        if ($user->favorites()->where('product_id', $product->id)->exists()) {
            $user->favorites()->detach($product->id);
            $message = 'Product removed from favorites';
        } else {
            $user->favorites()->attach($product->id);
            $message = 'Product added to favorites';
        }

        return response()->json(['message' => $message]);
    }

    public function favorites()
    {
        $favorites = Auth::user()->favorites()->with(['category', 'user'])->paginate(12);
        return response()->json($favorites);
    }

    public function userProducts()
    {
        $products = Auth::user()->products()->with('category')->latest()->paginate(12);
        return response()->json($products);
    }
}
