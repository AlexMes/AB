<?php

namespace App\Integrations\Drivers;

use App\Integrations\Form;
use App\Integrations\Payload;
use App\Lead;
use Zttp\ZttpResponse;

class ConvertingTeamDriver implements FormDriver
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
            'firstName'              => $lead->firstname,
            'lastName'               => $lead->lastname,
            'email'                  => $lead->email,
            'phone'                  => $this->formatPhone($lead->phone, $this->form->phone_prefix),
            'phonecc'                => $this->form->phone_prefix,
            'password'               => rand(100, 999) . '#wE&ty',
            'project'                => 'apibrokers',                   //project name static
            'lang'                   => 'ru',
            'a'                      => intval($this->form->external_affiliate_id),   // affiliate ID
            'o'                      => intval($this->form->external_offer_id),       //offer ID
            's'                      => implode('|', [
                $lead->domain,
                $lead->clickid,
                $payload->id,
                $lead->id,
                $lead->ip,
                $lead->utm_campaign,
                $lead->utm_source,
                $lead->utm_content,
                $lead->utm_term,
                $lead->utm_medium,
            ]),
            'agreement'              => true,
            'agreementBroker'        => true,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getExternalId(ZttpResponse $response)
    {
        return $response->json()['statusCode'] == 201 && $response->json()['regId'] ? $response->json()['regId'] : null;
    }

    private function formatPhone($phone, $prefix)
    {
        $phone = (mb_substr($phone, 0, 1) == 8)
            ? substr_replace($phone, 7, 0, 1) :
            $phone;

        return substr($phone, strlen(str_replace('+', '', $prefix)));
    }
}
