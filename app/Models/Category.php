<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Category extends Model implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['name', 'description'];
    
    protected $fillable = [
        'slug',
        'image',
        'parent_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            // Generate slug from the default locale's name
            $defaultLocale = config('app.locale');
            $category->slug = Str::slug($category->translate($defaultLocale)->name);
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $defaultLocale = config('app.locale');
                $category->slug = Str::slug($category->translate($defaultLocale)->name);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
