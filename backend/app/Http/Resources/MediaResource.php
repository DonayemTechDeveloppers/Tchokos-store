<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin \App\Models\Media
 */
class MediaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'url' => $this->url ? Storage::disk('public')->url($this->url) : null,
            'poster_url' => $this->poster_url ? Storage::disk('public')->url($this->poster_url) : null,
            'position' => $this->position,
        ];
    }
}
