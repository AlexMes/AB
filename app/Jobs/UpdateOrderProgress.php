<?php

namespace App\Jobs;

use App\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateOrderProgress implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\Domain
     */
    protected $domain;

    /**
     * UpdateOrderProgress constructor.
     *
     * @param \App\Domain $domain
     *
     * @return void
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain->loadMissing('order');
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->domain->order_id !== null) {
            $this->domain->order->updateProgress();
        }
    }
}
