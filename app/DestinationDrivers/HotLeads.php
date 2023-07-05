<?php

namespace App\DestinationDrivers;

use App\DestinationDrivers\Contracts\DeliversLeadToDestination;
use App\Exceptions\DeliveryFailed;
use App\Lead;
use App\LeadDestination;
use App\Leads\PoolAnswer;
use App\Trail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HotLeads implements DeliversLeadToDestination
{
    protected string $url;
    protected string $managerId;
    protected string $statusId;
    protected string $token;
    protected string $isSuccessful;
    protected ?string $error;
    protected $redirect = null;

    public function __construct($configuration = null)
    {
        $this->url       = $configuration['url'];
        $this->managerId = $configuration['manager'];
        $this->statusId  = $configuration['status'];
        $this->token     = $configuration['token'];
    }

    public function send(Lead $lead): void
    {
        $response = Http::asForm()
            ->post(sprintf("%s/api/add_one_lid/%s", $this->url, $this->token), [
                'first_name'  => $lead->firstname,
                'last_name'   => $lead->lastname ?? $lead->middlename ?? 'Unknown',
                'phone'       => $lead->formatted_phone,
                'email'       => $lead->getOrGenerateEmail(),
                'source_lid'  => sprintf("%s-%s_%0d%s", optional(optional($lead->user)->branch)->name, Str::before($lead->offer->getOriginalCopy()->name, '_'), now()->month, now()->year),
                'manager_id'  => $this->managerId,
                'status_id'   => $this->statusId,
                'description' => $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $question) => $question->getQuestion().' -> '.$question->getAnswer())->implode(' | ') : ''
            ])->throw();

        if (! $response->ok()) {
            $this->isSuccessful = false;
            $this->error        = $response->body();
            throw new DeliveryFailed($response->body());
        }

        if ($response->ok()) {
            if ($response->offsetExists('error_key') && $response->offsetGet('error_key') != 0) {
                $this->error        = $response->body();
                $this->isSuccessful = false;
                throw new DeliveryFailed($response->body());
            }
            \Log::warning($response->body());
            $this->isSuccessful = true;
            try {
                $this->duplicateToPayments($lead);
            } catch (\Throwable $th) {
                logger('[pmt] failure for lead '.$lead->id);
                logger($th->getMessage());
            }
        }
    }

    public function duplicateToPayments(Lead $lead)
    {
        if ($this->token === 'j8g6132n1ber6t' && in_array($lead->offer_id, [829,620,586,665,727,583])) {
            app(Trail::class)->add('Going to pmt');
            logger('[pmt] sending lead '.$lead->id);
            $dst = LeadDestination::find(116)->initialize();

            $dst->send($lead);
            logger('[pmt] sent lead '.$lead->id);
            app(Trail::class)->add('Sent to pmt');
            if ($dst->isDelivered()) {
                app(Trail::class)->add('Delivered');
                logger('[pmt] delivered '.$lead->id);
                logger('[pmt] redirect url is '.$dst->getRedirectUrl());
                app(Trail::class)->add('Redirect '.$dst->getRedirectUrl());
                $this->redirect = $dst->getRedirectUrl();
            } else {
                app(Trail::class)->add('Cant deliver '.$dst->getError());
                logger('failed to deliver '.$lead->id.''.$dst->getError());
            }
        }

        if ($this->token === 'j8g6132n1ber6t' && in_array($lead->offer_id, [645,812,967,966])) {
            app(Trail::class)->add('Going to pmt tn');
            logger('[pmt] sending lead '.$lead->id);
            $dst = LeadDestination::find(131)->initialize();

            $dst->send($lead);
            logger('[pmt] sent lead '.$lead->id);
            app(Trail::class)->add('Sent to pmt');
            if ($dst->isDelivered()) {
                app(Trail::class)->add('Delivered');
                logger('[pmt] delivered '.$lead->id);
                logger('[pmt] redirect url is '.$dst->getRedirectUrl());
                app(Trail::class)->add('Redirect '.$dst->getRedirectUrl());
                $this->redirect = $dst->getRedirectUrl();
            } else {
                app(Trail::class)->add('Cant deliver '.$dst->getError());
                logger('failed to deliver '.$lead->id.''.$dst->getError());
            }
        }

        if ($this->token === 'j8g6132n1ber6t' && in_array($lead->offer_id, [816])) {
            app(Trail::class)->add('Going to pmt tn');
            logger('[pmt] sending lead '.$lead->id);
            $dst = LeadDestination::find(135)->initialize();

            $dst->send($lead);
            logger('[pmt] sent lead '.$lead->id);
            app(Trail::class)->add('Sent to pmt');
            if ($dst->isDelivered()) {
                app(Trail::class)->add('Delivered');
                logger('[pmt] delivered '.$lead->id);
                logger('[pmt] redirect url is '.$dst->getRedirectUrl());
                app(Trail::class)->add('Redirect '.$dst->getRedirectUrl());
                $this->redirect = $dst->getRedirectUrl();
            } else {
                app(Trail::class)->add('Cant deliver '.$dst->getError());
                logger('failed to deliver '.$lead->id.''.$dst->getError());
            }
        }
    }
    public function isDelivered(): bool
    {
        return $this->isSuccessful;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->redirect;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return null;
    }
}
