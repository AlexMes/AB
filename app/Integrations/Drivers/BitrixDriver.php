<?php

namespace App\Integrations\Drivers;

use App\Integrations\Form;
use App\Integrations\Payload;
use App\Lead;
use Zttp\ZttpResponse;

class BitrixDriver implements FormDriver
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
            'fields' => [
                'TITLE'                           => 'Lead from' . $lead->domain . ' #' . $lead->id,
                'NAME'                            => $lead->firstname,
                'LAST_NAME'                       => $lead->lastname,
                'SECOND_NAME'                     => $lead->middlename,
                'SOURCE_DESCRIPTION'              => $lead->domain,
                'COMPANY_TITLE'                   => 'unknown',
                'EMAIL'                           => [
                    "n0" => [
                        "VALUE"      => $lead->email,
                        "VALUE_TYPE" => "WORK",
                    ],
                ],
                'EMAIL_WORK'    => $lead->email,
                'PHONE'         => [
                    "n0" => [
                        "VALUE"      => $lead->formatted_phone,
                        "VALUE_TYPE" => "WORK",
                    ],
                ],
                'PHONE_MOBILE'  => $lead->formatted_phone,
                'COMMENTS'      => $lead->id,
            ],
            'params' => ["REGISTER_SONET_EVENT" => "Y"],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getExternalId(ZttpResponse $response)
    {
        return $response->json()['result'] ?? null;
    }
}
