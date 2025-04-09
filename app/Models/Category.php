<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'slug',
        'image',
        'parent_id'
    ];

    protected $hidden = ['translations'];

    protected $appends = ['translated_name', 'translated_description'];

    public function getTranslatedNameAttribute()
    {
        return $this->getTranslated('name');
    }

    public function getTranslatedDescriptionAttribute()
    {
        return $this->getTranslated('description');
    }

    protected function getTranslated($attribute)
    {
        $locale = app()->getLocale();
        return $this->getTranslation($attribute, $locale, false) ?? $this->getTranslation($attribute, config('app.fallback_locale'));
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($category) {
    //         // Generate slug from the default locale's name
    //         $defaultLocale = config('app.locale');
    //         $category->slug = Str::slug($category->translate($defaultLocale)->name);
    //     });

    //     static::updating(function ($category) {
    //         if ($category->isDirty('name')) {
    //             $defaultLocale = config('app.locale');
    //             $category->slug = Str::slug($category->translate($defaultLocale)->name);
    //         }
    //     });
    // }

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
