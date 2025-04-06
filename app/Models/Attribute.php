<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Attribute extends Model implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['name'];
    
    protected $fillable = [
        'type',
        'category_id',
        'is_required',
        'is_filterable',
        'validation_rules'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'validation_rules' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }
}
