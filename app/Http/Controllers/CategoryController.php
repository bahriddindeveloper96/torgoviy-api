<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Attribute;
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
            'translations.*.locale' => 'required|string|in:uz,ru,en',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
            'parent_id' => 'nullable|exists:categories,id',
            'attributes' => 'array',
            'attributes.*.translations' => 'required|array',
            'attributes.*.translations.*.locale' => 'required|string|in:uz,ru,en',
            'attributes.*.translations.*.name' => 'required|string|max:255',
            'attributes.*.type' => 'required|string|in:text,select,number',
            'attributes.*.is_required' => 'required|boolean',
            'attributes.*.is_filterable' => 'required|boolean',
            'attributes.*.validation_rules' => 'nullable|array'
        ]);

        // Get name from default locale (uz)
        $defaultName = collect($request->translations)
            ->where('locale', 'uz')
            ->first()['name'];

        // Generate slug from default name
        $slug = \Str::slug($defaultName);

        // Check if slug exists and make it unique if needed
        $count = 1;
        $originalSlug = $slug;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // Create category with translations and slug
        $category = new Category();
        $category->slug = $slug;
        $category->parent_id = $request->parent_id;
        $category->setTranslations('name', collect($request->translations)->pluck('name', 'locale')->toArray());
        $category->setTranslations('description', collect($request->translations)->pluck('description', 'locale')->toArray());
        $category->save();

        // Create attributes if provided
        if ($request->has('attributes')) {
            foreach ($request->attributes as $attributeData) {
                $attribute = new Attribute();
                $attribute->type = $attributeData['type'];
                $attribute->is_required = $attributeData['is_required'];
                $attribute->is_filterable = $attributeData['is_filterable'];
                $attribute->validation_rules = $attributeData['validation_rules'] ?? [];
                $attribute->setTranslations('name', collect($attributeData['translations'])->pluck('name', 'locale')->toArray());
                $attribute->category()->associate($category);
                $attribute->save();
            }
        }

        // Load category with attributes and their translations
        $category->load(['attributes' => function ($query) {
            $query->with('translations');
        }]);

        return response()->json($category, 201);
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
