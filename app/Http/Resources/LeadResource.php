<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
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
        return array_merge(
            parent::toArray($request),
            ['is_duplicate' => $this->is_duplicate],
            ['deleted_at'   => $this->deleted_at],
        );
    }
}
