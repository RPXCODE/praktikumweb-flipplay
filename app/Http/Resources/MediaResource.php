<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            'title' => $this->title,
            'type' => $this->type,
            'genre' => $this->genre,
            'duration_minutes' => $this->duration_minutes,
            'formatted_duration' => $this->formatted_duration,
            'release_year' => $this->release_year,
            'director' => $this->when($this->type === 'movie', $this->director),
            'age_rating' => $this->when($this->type === 'movie', $this->age_rating),
            'artist' => $this->when($this->type === 'music', $this->artist),
            'album' => $this->when($this->type === 'music', $this->album),
            // Menggunakan whenLoaded sesuai ketentuan demo Modul 4
            'collected_by_users' => UserResource::collection($this->whenLoaded('users')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
