<?php

namespace App\Integrations\Drivers;

use App\Integrations\Form;
use App\Integrations\Payload;
use App\Lead;
use Zttp\ZttpResponse;

interface FormDriver
{
    /**
     * Generate request from lead
     *
     * @param Lead    $lead
     * @param Payload $payload
     *
     * @return array|boolean|null
     */
    public function prepareParamsRequest(Lead $lead, Payload $payload);

    /**
     * Parse request after form send
     *
     * @param $response
     *
     * @return string
     */
    public function getExternalId(ZttpResponse $response);
}
