<?php

namespace App\Http\Resources;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Link
 */
class LinkResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'short_url' => $this->short_url,
            'destination' => $this->destination,
            'visits' => $this->visits,
            'last_visited_at' => $this->last_visited_at,
            'created_at' => $this->created_at,
        ];
    }
}
