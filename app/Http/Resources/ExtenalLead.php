<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * Class ExtenalLead
 *
 * @package App\Http\Resources
 *
 * @mixin \App\Lead
 */
class ExtenalLead extends JsonResource
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
            'id'           => $this->uuid,
            'name'         => $this->fullname,
            'phone'        => $this->formatted_phone,
            'ip'           => $this->ip,
            'domain'       => $this->domain,
            'clickid'      => $this->clickid,
            'utm_source'   => $this->utm_source,
            'utm_campaign' => $this->utm_campaign,
            'utm_term'     => $this->utm_term,
            'utm_content'  => $this->utm_content,
            'status'       => $this->status(),
            $this->mergeWhen($this->hasDeposits() && Carbon::parse(optional($this->deposits()->first())->date)->lessThanOrEqualTo(Carbon::parse('2022-07-18 00:00:00')), [
                'ftdDate' => $this->deposits->first()->date ?? null,
            ]),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }

    protected function status()
    {
        $status = optional($this->assignments()->first())->status ?? 'Новый';

        if ($status === 'Депозит') {
            if (Carbon::parse(optional($this->deposits()->first())->date)->lessThanOrEqualTo(Carbon::parse('2022-07-18 00:00:00'))) {
                return 'Депозит';
            }

            return 'В работе у клоузера';
        }

        return $status;
    }
}
