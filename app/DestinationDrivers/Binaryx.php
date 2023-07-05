<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Binaryx implements DeliversLeadToDestination
{
    /**
     * @var \Illuminate\Http\Client\Response
     */
    protected $response;

    public function __construct($configuration = null)
    {
        // do nothing
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->post('https://app.binaryx.com/exchangemw/account/sign-up?utm_source=media_buying&utm_medium=buyer_33&utm_campaign=buyer_33_campaign_1', [
            'first_name'            => $lead->firstname,
            'last_name'             => $lead->lastname,
            'email'                 => $lead->email,
            'country_id'            => '54b64217-2da0-4196-85cd-91abfa1539f2',
            'password'              => $lead->requestData['password'],
            'confirm_aml_policy'    => "true",
            'confirm_refund_policy' => "true",
            'geetest_challenge'     => $lead->requestData['geetest_challenge'],
            'geetest_validate'      => $lead->requestData['geetest_validate'],
            'geetest_seccode'       => $lead->requestData['geetest_seccode'],
        ]);
    }

    public function isDelivered(): bool
    {
        return $this->response->successful();
    }

    public function getError(): ?string
    {
        return Str::limit($this->response->body(), 250);
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    public function getExternalId(): ?string
    {
        return null;
    }
}
