<?php

namespace App\Integrations\Jobs;

use App\Integrations\Form;
use App\Integrations\Payload;
use App\Lead;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Zttp\Zttp;

class LeadSender implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use SerializesModels;

    /**
     * @var \App\Lead
     */
    public $lead;

    /**
     * @var \App\Integrations\Form
     */
    public $form;

    /**
     * @var \App\Integrations\Payload
     */
    public $payload;

    /**
     * @var int
     */
    protected $delayBetweenTries = 20;

    /**
     * @var int
     */
    public $tries = 5;

    public function __construct(Lead $lead, Form $form)
    {
        $this->lead  = $lead;
        $this->form  = $form;
    }

    /**
     * Execute the job
     *
     * @return void
     */
    public function handle()
    {
        if ($this->form->status && $this->lead->external_id == null) {
            $this->send();
        }
    }

    /**
     * @return mixed
     */
    public function send()
    {
        $this->payload = $this->form->payloads()->create([
            'lead_id'           => $this->lead->id,
            'landing_id'        => $this->lead->landing_id,
            'offer_id'          => $this->lead->offer_id,
            'status'            => Payload::PREPARED,
        ]);
        $this->payload->update([
            'requestContents'   => $this->prepareFields()
        ]);

        try {
            $response = Zttp::timeout(360)->withoutVerifying()->withHeaders([
                'X-Forwarded-For' => $this->lead->ip,
            ])->{$this->form->method}($this->form->url, $this->prepareFields());

            if ($response->isOk()) {
                return $this->payload->succeeded($response);
            }

            return $this->payload->failed($response);
        } catch (Exception $exception) {
            $this->payload->fatal($exception);
        }
    }


    private function prepareFields()
    {
        return $this->form->getProvider()->prepareParamsRequest($this->lead, $this->payload);
    }

    /**
     * Retry for an hour
     *
     * @return void
     */
    public function retryUntil()
    {
        return now()->addHour();
    }
    /**
     * Tag queued job
     *
     * @return array
     */
    public function tags()
    {
        return ['form', 'lead_sender'];
    }
}
