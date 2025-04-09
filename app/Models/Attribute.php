<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Attribute extends Model
{
    use HasTranslations;

    public $translatable = ['name']; // translatable attributes
    protected $fillable = [
        'type',
        'name',
        'category_id',
        'is_required',
        'is_filterable',
        'validation_rules'
    ];

    protected $hidden = ['translations'];

    protected $appends = ['translated_name'];

    protected $casts = [
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'validation_rules' => 'array'
    ];

    public function getTranslatedNameAttribute()
    {
        return $this->getTranslated('name');
    }

    protected function getTranslated($attribute)
    {
        $locale = app()->getLocale();
        return $this->getTranslation($attribute, $locale, false) ?? $this->getTranslation($attribute, config('app.fallback_locale'));
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }
}
