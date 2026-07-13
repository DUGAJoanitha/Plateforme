<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'description' => $this->description,
            'status' => $this->status,
            'budget_total' => $this->budget_total,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'risk_score' => $this->risk_score,
            'organisation_id' => $this->org_id,
            'programme_id' => $this->programme_id,
            'programme' => $this->whenLoaded('programme', fn() => new ProgrammeResource($this->programme)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
