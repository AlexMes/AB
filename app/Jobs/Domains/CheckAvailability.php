<?php

namespace App\Jobs\Domains;

use App\AdsBoard;
use App\Domain;
use App\Notifications\Domain\Down;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

class CheckAvailability implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Domain to check
     *
     * @var \App\Domain
     */
    protected $domain;

    /**
     * Send notification or not
     *
     * @var bool
     */
    protected bool $notify = false;

    /**
     * Create a new job instance.
     *
     * @param Domain $domain
     * @param mixed  $notify
     */
    public function __construct(Domain $domain, $notify = false)
    {
        $this->domain = $domain;
        $this->notify = $notify;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            /** @var \Illuminate\Http\Client\Response */
            $response = Http::retry(2, 3)->timeout(5)->get($this->domain->url);
            if ($response->successful()) {
                $this->domain->isUp();
            } else {
                if ($this->notify) {
                    if ($this->domain->down_since === null) {
                        $recipients = [
                            AdsBoard::devsChannel(),
                            optional(optional($this->domain->offer)->branch)->telegram_id
                        ];

                        Notification::send($recipients, new Down($this->domain));
                        $this->domain->update(['down_since' => now()->subMinutes(2)]);
                    }
                } else {
                    $this->domain->isDown();
                }
            }
        } catch (\Throwable $th) {
            if ($this->notify) {
                if ($this->domain->down_since === null) {
                    $recipients = [
                        AdsBoard::devsChannel(),
                        optional(optional($this->domain->offer)->branch)->telegram_id
                    ];

                    Notification::send($recipients, new Down($this->domain));
                    $this->domain->update(['down_since' => now()->subMinutes(2)]);
                }
            } else {
                $this->domain->isDown();
            }
        }
    }

    /**
     * Determine necro landings
     *
     * @return bool
     */
    protected function isNecro()
    {
        $latestLead = $this->domain->leads()->latest()->value('created_at');
        // no leads on the domain
        if ($latestLead === null) {
            // if domain is fresh - it is ok
            // if created more than 2 days ago - fucking necro
            return $this->domain->created_at->lte(now()->subDays(2));
        }

        // last lead was fucking month ago - is necro
        if (now()->diffInDays($latestLead) > 30) {
            return true;
        }

        return false;
    }
}
