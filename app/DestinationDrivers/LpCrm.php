<?php

namespace App\DestinationDrivers;

use App\AdsBoard;
use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use Illuminate\Support\Facades\Http;

class LpCrm implements DeliversLeadToDestination
{
    protected $url;
    protected $token;
    protected $good;
    /**
     * Undocumented variable
     *
     * @var \Illuminate\Http\Client\Response
     */
    protected $response;

    public function __construct($configuration = null)
    {
        $this->url   = $configuration['url'];
        $this->token = $configuration['token'];
        $this->good  = $configuration['good'];
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->post($this->url.'/api/addNewOrder.html', [
            'key'          => $this->token,
            'order_id'     => number_format(round(microtime(true) * 10), 0, '.', ''),
            'country'      => "UA",
            'products'     => urlencode(serialize([[
                'product_id' => $this->good,
                'price'      => 250,
                'count'      => 1
            ]])),
            'site'         => $lead->domain,
            'bayer_name'   => $lead->fullname,
            'phone'        => $lead->formatted_phone,
            'email'        => $lead->getOrGenerateEmail(),
        ]);
        try {
            $data = json_decode($this->response->body(), true)['data'];

            AdsBoard::report($data['order_id']);
        } catch (\Throwable $th) {
            AdsBoard::report($th->getMessage());
        }
        AdsBoard::report($this->response->status());
        AdsBoard::report($this->response->body());
    }

    public function isDelivered(): bool
    {
        return $this->response->ok();
    }

    public function getError(): ?string
    {
        return $this->response->body();
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    public function getExternalId(): ?string
    {
        if ($this->response->offsetExists('data')) {
            return $this->response->offsetGet('data')['order_id'];
        }

        return null;
    }
}
