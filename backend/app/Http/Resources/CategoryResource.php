<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Category
 */
class CategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imagePath = $this->firstProductImage()?->url;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type->value,
            'position' => $this->position,
            'image_url' => $imagePath ? Storage::disk('public')->url($imagePath) : null,
            'children' => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
