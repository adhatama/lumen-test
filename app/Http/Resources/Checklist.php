<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Checklist extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'object_domain' => $this->object_domain,
            'object_id' => $this->object_id,
            'description' => $this->description,
            'is_completed' => $this->is_completed,
            'completed_at' => $this->completed_at,
            'due' => $this->due,
            'urgency' => $this->urgency,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
