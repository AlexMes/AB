<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExternalAccount extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'account_id'    => $this->account_id,
            'name'          => $this->name,
            'spend'         => $this->spend,
            'lifetime'      => $this->lifetime,
            'is_banned'     => $this->banned_at !== null,
        ];
    }
}
