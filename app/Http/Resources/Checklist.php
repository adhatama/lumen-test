<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Checklist extends JsonResource
{
    private $withItems;

    public function __construct($resource, $withItems = false)
    {
        parent::__construct($resource);
        $this->resource = $resource;

        $this->withItems = $withItems;
    }

    public function toArray($request)
    {
        $data = [
            'type' => 'checklists',
            'id' => $this->id,
            'attributes' => [
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
            ],
            'links' => [
                'self' => route('checklists.get', ['id' => $this->id]),
            ],
        ];

        $data['included'] = $this->when($this->items, Item::collection($this->items));

        return $data;
    }
}
