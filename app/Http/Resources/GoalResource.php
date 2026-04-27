<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'title'                 => $this->title,
            'description'           => $this->description,
            'deadline'              => $this->deadline?->toDateString(),
            'status'                => $this->status,
            'tasks_count'           => $this->whenCounted('tasks'),
            'completed_tasks_count' => $this->whenCounted('tasks', fn() => $this->completed_tasks_count ?? 0),
            'tasks'                 => TaskResource::collection($this->whenLoaded('tasks')),
            'created_at'            => $this->created_at->toIso8601String(),
            'updated_at'            => $this->updated_at->toIso8601String(),
        ];
    }
}
