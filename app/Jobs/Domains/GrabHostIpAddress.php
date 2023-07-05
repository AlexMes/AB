<?php

namespace App\Jobs\Domains;

use App\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GrabHostIpAddress implements ShouldQueue
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
     * Create a new job instance.
     *
     * @param Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $ip = dns_get_record(basename($this->domain->url), DNS_A);

            $this->domain->update(['ip' => $ip[0] ?? null]);
        } catch (\Throwable $exception) {
            Log::error('Error domain ip. ' . $exception->getMessage());
        }
    }
}
