<?php

namespace App\Jobs\Leads;

use App\IpAddress;
use App\Lead;
use App\Services\IpApi\IpApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchIpAddressData implements ShouldQueue
{
    use SerializesModels;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    /**
     * @var \App\Lead
     */
    protected Lead $lead;

    /**
     * FetchIpAddressData constructor.
     *
     * @param \App\Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead->fresh();
    }

    /**
     * Execute job
     *
     * @param \App\Services\IpApi\IpApi $api
     *
     * @throws \Exception
     */
    public function handle(IpApi $api)
    {
        if ($this->shouldRun()) {
            $ipAddress = retry(3, fn () => IpAddress::store($api->get($this->lead->ip)));

            $this->lead->addEvent(
                Lead::IP_FETCHED,
                !empty($ipAddress) ? ['id' => $ipAddress->id, 'ip' => $ipAddress->ip] : null
            );
        }
    }

    /**
     * @return bool
     */
    protected function shouldRun(): bool
    {
        return $this->lead->ip !== null
            && $this->lead->ip !== '127.0.0.1'
            && IpAddress::whereIp($this->lead->ip)->doesntExist();
    }
}
