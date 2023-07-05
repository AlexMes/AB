<?php


namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Lead;
use App\Leads\PoolAnswer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TimGroup implements DeliversLeadToDestination
{
    protected $key;
    protected $response;
    protected $lang;

    public function __construct($configuration = null)
    {
        $this->key  = $configuration['key'];
        $this->lang = $configuration['lang'] ?? 'ru';
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::post('https://timgroup.pro/api/get-form', $payload = [
            'key'       => $this->key,
            'firstname' => $lead->firstname,
            'lastname'  => $lead->lastname ?? 'Unknown',
            'email'     => $lead->getOrGenerateEmail(),
            'phone'     => $lead->phone,
            'geo'       => optional($lead->ipAddress)->country_code ?? optional($lead->lookup)->country,
            'funnel'    => $this->funnel($lead),
            'lang'      => $this->lang,
            'ip2'       => $lead->ip,
            'cid'       => $lead->uuid,
            // 'comment'   => $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $question) => $question->getQuestion().'-> '.$question->getAnswer())->implode(PHP_EOL) : '',
        ]);

        $lead->addEvent('payload', $payload);
        $lead->addEvent('result', [
            'status' => $this->response->status(),
            'body'   => $this->response->body()
        ]);
    }

    protected function funnel(Lead $lead)
    {
        if ($this->key === 'RHtYTOOeHP4NnlgH') {
            return Str::before($lead->offer->getOriginalCopy()->name, '_');
        }

        return $lead->offer->description ?? Str::before($lead->offer->getOriginalCopy()->name, '_');

        // return $lead->hasPoll() ? $lead->getPollAsUrl() : $lead->domain ?? ;
    }
    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'status') === 1;
    }

    public function getError(): ?string
    {
        return data_get($this->response->json(), 'errors');
    }

    public function getRedirectUrl(): ?string
    {
        return data_get($this->response->json(), 'autologin_link');
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'id');
    }
}
