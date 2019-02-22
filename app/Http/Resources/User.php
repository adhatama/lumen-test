<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'token' => $this->token,
        ];
    }
}
