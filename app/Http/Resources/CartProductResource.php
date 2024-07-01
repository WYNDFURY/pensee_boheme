<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->pivot->quantity,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'image' => $this->image,
            'category_id' => $this->category_id,
            'stock' => $this->stock,
            'is_active' => $this->is_active,
            'category' => $this->when($this->category, fn () => new CategoryResource($this->category)),
            'total' => $this->pivot->quantity * $this->price,
        ];
    }
}
