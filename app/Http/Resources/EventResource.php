<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'start_at'        => $this->start_at->toIso8601String(),
            'end_at'          => $this->end_at->toIso8601String(),
            'is_recurring'    => $this->is_recurring,
            'recurrence_rule' => $this->recurrence_rule,
            'color'           => $this->color,
            'created_at'      => $this->created_at->toIso8601String(),
            'updated_at'      => $this->updated_at->toIso8601String(),
        ];
    }
}
