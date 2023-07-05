<?php

namespace App\Integrations\Drivers;

use App\Integrations\Form;
use App\Integrations\Payload;
use App\Lead;
use Zttp\ZttpResponse;

class TrafficonDriver implements FormDriver
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
            'email'                         => $lead->email ?? 'temp' . time() . '@gmail.com',
            'area_code'                     => $this->form->phone_prefix,
            'phone'                         => $this->formatPhone($lead->formatted_phone, $this->form->phone_prefix),
            'password'                      => rand(100, 999) . 'DMPasD',
            'country'                       => 'RU',
            'iso'                           => 'RU',
            'ip'                            => $lead->ip,
            'aff_sub1'                      => $lead->id,
            'aff_sub2'                      => $lead->clickid,
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
        return $response->json()['statusCode'] == 201 && $response->json()['lead_id'] ? $response->json()['lead_id'] : null;
    }

    private function formatPhone($phone, $prefix)
    {
        return substr($phone, strlen(str_replace('+', '', $prefix)));
    }
}
