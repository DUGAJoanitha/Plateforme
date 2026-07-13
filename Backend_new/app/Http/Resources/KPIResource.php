<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KPIResource extends JsonResource
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
            'project_id' => $this->project_id,
            'name' => $this->name,
            'description' => $this->description,
            'target_value' => $this->target_value,
            'current_value' => $this->current_value,
            'baseline' => $this->baseline,
            'unit' => $this->unit,
            'frequency' => $this->frequency,
            'performance' => $this->calculatePerformance(),
            'is_on_track' => $this->isOnTrack(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
