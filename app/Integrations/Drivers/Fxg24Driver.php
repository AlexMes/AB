<?php

namespace App\Integrations\Drivers;

use App\Integrations\Form;
use App\Integrations\Payload;
use App\Lead;
use Illuminate\Support\Str;
use Zttp\ZttpResponse;

class Fxg24Driver implements FormDriver
{
    private Form $form;

    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    /**
     * @inheritDoc
     */
    public function prepareParamsRequest(Lead $lead, Payload $payload)
    {
        return [
            'fname'                             => $lead->firstname,
            'lname'                             => $lead->lastname,
            'email'                             => $lead->email ?? Str::slug($lead->fullname, '') . rand(1, 99) . '@gmail.com',
            'fullphone'                         => '+' . $lead->formatted_phone,
            'ip'                                => $lead->ip,
            'domain'                            => $lead->domain,
            'campaign_id'                       => intval($this->form->external_affiliate_id),
            'link_id'                           => intval($this->form->external_offer_id),
            'utm_campaign'                      => $lead->utm_campaign,
            'utm_source'                        => $lead->utm_source,
            'utm_content'                       => $lead->utm_content,
            'utm_term'                          => $lead->utm_term,
            'utm_media'                         => $lead->utm_medium,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getExternalId(ZttpResponse $response)
    {
        return $response->json()['id'] ?? null;
    }
}
