<?php

namespace App\Integrations\Drivers;

use App\Integrations\Form;
use App\Integrations\Payload;
use App\Lead;
use Zttp\ZttpResponse;

class DefaultDriver implements FormDriver
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
            'id'        => $this->form->form_id,
            'api_key'   => $this->form->form_api_key,
            'umcfields' => [
                'id1'                  => $lead->id,
                'imya'                 => $lead->firstname,
                'familiya1'            => $lead->lastname,
                'telefon'              => $lead->phone,
                'email'                => $lead->email,
                'domain'               => $lead->domain,
                'userip'               => $lead->ip,
                'utmcampaign'          => $lead->utm_campaign,
                'utmsource'            => $lead->utm_source,
                'utmcontent'           => $lead->utm_content,
                'utmterm'              => $lead->utm_term,
                'utmmedium'            => $lead->utm_medium,
                'binomclickid'         => $lead->clickid,
                'dataizmeneniyapolya'  => $lead->created_at,
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function getExternalId(ZttpResponse $response)
    {
        return $response->json()['success'] ? intval($response->json()['message']) : null;
    }
}
