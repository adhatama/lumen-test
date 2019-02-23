<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Item extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'type' => 'items',
            'id' => $this->id,
            'attributes' => [
                'description' => $this->description,
                'is_completed' => $this->is_completed,
                'completed_at' => $this->completed_at,
                'due' => $this->due,
                'urgency' => $this->urgency,
                'updated_by' => $this->updated_by,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'links' => [
                'self' => route('checklists.items.get', [
                    'checklistId' => $this->checklist->id,
                    'itemId' => $this->id,
                ]),
            ],
        ];

        return $data;
    }
}
