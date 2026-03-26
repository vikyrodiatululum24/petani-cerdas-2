<?php

namespace App\Http\Resources\Testimonial;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role,
            'content' => $this->content,
            'short_content' => Str::limit($this->content, 180),
            'rating' => (int) $this->rating,
            'avatar' => Str::upper(Str::of($this->name)->trim()->explode(' ')->take(2)->map(fn (string $part) => Str::substr($part, 0, 1))->join('')),
            'avatar_color' => $this->avatar_color,
            'is_published' => (bool) $this->is_published,
            'sort_order' => (int) $this->sort_order,
            'published_at' => optional($this->published_at)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
