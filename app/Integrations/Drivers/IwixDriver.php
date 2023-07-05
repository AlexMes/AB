<?php

namespace App\Integrations\Drivers;

use App\Integrations\Form;
use App\Integrations\Payload;
use App\Lead;
use Illuminate\Support\Str;
use Zttp\ZttpResponse;

class IwixDriver implements FormDriver
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
            'first_name'                    => $lead->firstname,
            'last_name'                     => $lead->lastname,
            'email'                         => $lead->email ?? Str::slug($lead->fullname, '') . rand(1, 99) . '@gmail.com',
            'phonecc'                       => $this->form->phone_prefix,
            'phone'                         => $this->formatPhone($lead->formatted_phone, $this->form->phone_prefix),
            'password'                      => rand(100, 999) . 'DMPasD',
            'country'                       => 'RU',
            'user_ip'                       => $lead->ip,
            'aff_sub'                       => $lead->clickid,
            'aff_sub1'                      => $lead->id,
            'aff_sub3'                      => $lead->domain,
            'aff_id'                        => $this->form->external_affiliate_id,
            'offer_id'                      => $this->form->external_offer_id,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getExternalId(ZttpResponse $response)
    {
        return $response->json()['lead_id'] ?? null;
    }

    private function formatPhone($phone, $prefix)
    {
        return substr($phone, strlen(str_replace('+', '', $prefix)));
    }
}
