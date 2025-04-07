<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'title',
        'description',
        'price',
        'category_id',
        'user_id',
        'condition',
        'location',
        'views'
    ];

    protected $casts = [
        'price' => 'float',
    ];

    protected $with = ['attributes', 'images'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function getAttribute($key)
    {
        $attribute = parent::getAttribute($key);

        if ($attribute === null) {
            // Check if this is a custom attribute
            $productAttribute = $this->attributes()->whereHas('attribute', function ($query) use ($key) {
                $query->where('name', $key);
            })->first();

            if ($productAttribute) {
                return $productAttribute->value;
            }
        }

        return $attribute;
    }
}
