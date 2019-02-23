<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Template extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'type' => 'templates',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'checklist' => [
                    'description' => $this->description,
                    'due_interval' => $this->due_interval,
                    'due_unit' => $this->due_unit,
                ],
            ],
            'links' => [
                'self' => route('checklists.templates.get', [
                    'id' => $this->id,
                ]),
            ],
        ];

        $data['attributes']['items'] = [];
        if ($this->items) {
            foreach ($this->items as $item) {
                $val['description'] = $item->description;
                $val['urgency'] = $item->urgency;
                $val['due_interval'] = $item->due_interval;
                $val['due_unit'] = $item->due_unit;

                array_push($data['attributes']['items'], $val);
            }
        }

        return $data;
    }
}
