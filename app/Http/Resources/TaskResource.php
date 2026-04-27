<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'description'      => $this->description,
            'priority'         => $this->priority,
            'status'           => $this->status,
            'goal_id'          => $this->goal_id,
            'goal'             => new GoalResource($this->whenLoaded('goal')),
            'scheduled_at'     => $this->scheduled_at?->toIso8601String(),
            'duration_minutes' => $this->duration_minutes,
            'category'         => $this->category,
            'created_at'       => $this->created_at->toIso8601String(),
            'updated_at'       => $this->updated_at->toIso8601String(),
        ];
    }
}
