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
        $categories = Category::whereNull('parent_id')
            ->with(['children', 'attributes'])
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->translated_name,
                    'description' => $category->translated_description,
                    'slug' => $category->slug,
                    'image' => $category->image,
                    'children' => $category->children->map(function ($child) {
                        return [
                            'id' => $child->id,
                            'name' => $child->translated_name,
                            'description' => $child->translated_description,
                            'slug' => $child->slug,
                            'image' => $child->image,
                        ];
                    }),
                    'attributes' => $category->attributes->map(function ($attribute) {
                        return [
                            'id' => $attribute->id,
                            'name' => $attribute->translated_name,
                            'type' => $attribute->type,
                            'is_required' => $attribute->is_required,
                            'is_filterable' => $attribute->is_filterable,
                            'validation_rules' => $attribute->validation_rules
                        ];
                    })
                ];
            });

        return response()->json($categories);
    }

    public function show(Category $category)
    {
        $category->load(['children', 'attributes']);
        
        $data = [
            'id' => $category->id,
            'name' => $category->translated_name,
            'description' => $category->translated_description,
            'slug' => $category->slug,
            'image' => $category->image,
            'children' => $category->children->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->translated_name,
                    'description' => $child->translated_description,
                    'slug' => $child->slug,
                    'image' => $child->image,
                ];
            }),
            'attributes' => $category->attributes->map(function ($attribute) {
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->translated_name,
                    'type' => $attribute->type,
                    'is_required' => $attribute->is_required,
                    'is_filterable' => $attribute->is_filterable,
                    'validation_rules' => $attribute->validation_rules
                ];
            })
        ];

        return response()->json($data);
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

        // Get breadcrumbs data
        $breadcrumbs = [];
        $currentCategory = $category;
        
        while ($currentCategory) {
            array_unshift($breadcrumbs, [
                'id' => $currentCategory->id,
                'name' => $currentCategory->translated_name,
                'description' => $currentCategory->translated_description,
                'slug' => $currentCategory->slug,
                'image' => $currentCategory->image
            ]);
            $currentCategory = $currentCategory->parent;
        }

        return response()->json([
            'breadcrumbs' => $breadcrumbs,
            'products' => $products
        ]);
    }
}
