<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'industry_id' => $this->industry_id,
            'industry' => $this->whenLoaded('industry', function (): array {
                return [
                    'id' => $this->industry->id,
                    'name' => $this->industry->name,
                    'slug' => $this->industry->slug,
                ];
            }),
        ];
    }
}
