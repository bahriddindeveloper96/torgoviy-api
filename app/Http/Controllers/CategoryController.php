<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show', 'products']);
    }

    public function index()
    {
        $categories = Category::with(['children', 'attributes', 'translations'])
            ->whereNull('parent_id')
            ->get();

        return response()->json($categories);
    }

    public function show(Category $category)
    {
        $category->load(['children', 'attributes', 'translations']);
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'translations' => 'required|array',
            'translations.*' => 'required|array',
            'translations.*.locale' => 'required|string|size:2',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048'
        ]);

        $category = new Category();
        $category->parent_id = $validated['parent_id'] ?? null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $category->image = $path;
        }

        $category->save();

        // Save translations
        foreach ($validated['translations'] as $translation) {
            $category->translateOrNew($translation['locale'])->fill([
                'name' => $translation['name'],
                'description' => $translation['description'] ?? null,
            ]);
        }
        $category->save();

        return response()->json($category->load('translations'), 201);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'translations' => 'required|array',
            'translations.*' => 'required|array',
            'translations.*.locale' => 'required|string|size:2',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:2048'
        ]);

        $category->parent_id = $validated['parent_id'] ?? null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $category->image = $path;
        }

        // Update translations
        foreach ($validated['translations'] as $translation) {
            $category->translateOrNew($translation['locale'])->fill([
                'name' => $translation['name'],
                'description' => $translation['description'] ?? null,
            ]);
        }
        $category->save();

        return response()->json($category->load('translations'));
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, 204);
    }

    public function products(Category $category)
    {
        $products = $category->products()
            ->with(['attributes', 'images'])
            ->paginate(20);

        // Get breadcrumbs data with translations
        $breadcrumbs = [];
        $currentCategory = $category->load('translations');
        
        while ($currentCategory) {
            array_unshift($breadcrumbs, [
                'id' => $currentCategory->id,
                'name' => $currentCategory->name,
                'description' => $currentCategory->description,
                'slug' => $currentCategory->slug,
                'translations' => $currentCategory->translations->keyBy('locale')
            ]);
            $currentCategory = $currentCategory->parent?->load('translations');
        }

        return response()->json([
            'breadcrumbs' => $breadcrumbs,
            'products' => $products
        ]);
    }
}
