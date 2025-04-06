<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    public function index()
    {
        $attributes = Attribute::with(['translations', 'category'])
            ->get();

        return response()->json($attributes);
    }

    public function show(Attribute $attribute)
    {
        $attribute->load(['translations', 'category']);
        return response()->json($attribute);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'translations' => 'required|array',
            'translations.*' => 'required|array',
            'translations.*.locale' => 'required|string|size:2',
            'translations.*.name' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,select,boolean',
            'category_id' => 'required|exists:categories,id',
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'validation_rules' => 'nullable|array'
        ]);

        $attribute = new Attribute();
        $attribute->type = $validated['type'];
        $attribute->category_id = $validated['category_id'];
        $attribute->is_required = $validated['is_required'] ?? false;
        $attribute->is_filterable = $validated['is_filterable'] ?? false;
        $attribute->validation_rules = $validated['validation_rules'] ?? null;
        
        $attribute->save();

        // Save translations
        foreach ($validated['translations'] as $translation) {
            $attribute->translateOrNew($translation['locale'])->fill([
                'name' => $translation['name']
            ]);
        }
        $attribute->save();

        return response()->json($attribute->load('translations'), 201);
    }

    public function update(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'translations' => 'required|array',
            'translations.*' => 'required|array',
            'translations.*.locale' => 'required|string|size:2',
            'translations.*.name' => 'required|string|max:255',
            'type' => 'required|string|in:text,number,select,boolean',
            'category_id' => 'required|exists:categories,id',
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'validation_rules' => 'nullable|array'
        ]);

        $attribute->type = $validated['type'];
        $attribute->category_id = $validated['category_id'];
        $attribute->is_required = $validated['is_required'] ?? false;
        $attribute->is_filterable = $validated['is_filterable'] ?? false;
        $attribute->validation_rules = $validated['validation_rules'] ?? null;

        // Update translations
        foreach ($validated['translations'] as $translation) {
            $attribute->translateOrNew($translation['locale'])->fill([
                'name' => $translation['name']
            ]);
        }
        $attribute->save();

        return response()->json($attribute->load('translations'));
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return response()->json(null, 204);
    }
}
