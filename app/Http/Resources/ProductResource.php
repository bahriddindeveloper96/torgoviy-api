<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => (float)$this->price,
            'category_id' => $this->category_id,
            'user_id' => $this->user_id,
            'condition' => $this->condition,
            'location' => $this->location,
            'views' => $this->views,
            'images' => $this->images->map(function($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'is_primary' => $image->is_primary
                ];
            }),
            'attributes' => ProductAttributeResource::collection($this->whenLoaded('attributes')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
