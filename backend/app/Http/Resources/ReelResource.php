<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * Reel (média vidéo) du « mur de reels » de l'accueil, avec le produit associé.
 *
 * @mixin \App\Models\Media
 */
class ReelResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url ? Storage::disk('public')->url($this->url) : null,
            'poster_url' => $this->poster_url ? Storage::disk('public')->url($this->poster_url) : null,
            // Vitrine autonome : une vidéo peut ne pas être rattachée à un produit.
            'product' => $this->whenLoaded('product', fn () => $this->product ? [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'slug' => $this->product->slug,
            ] : null),
        ];
    }
}
