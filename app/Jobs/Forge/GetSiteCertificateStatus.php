<?php

namespace App\Jobs\Forge;

use App\Services\Forge\ForgeClient;
use App\Services\Forge\RateLimited;
use App\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetSiteCertificateStatus implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Site $site;

    /**
     * Create a new job instance.
     *
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * Execute the job.
     *
     * @param ForgeClient $client
     *
     * @return void
     */
    public function handle(ForgeClient $client)
    {
        $this->site->update([
            'has_certificates' => $this->hasCertificates($client),
        ]);
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        return [new RateLimited];
    }

    /**
     * @param ForgeClient $client
     *
     * @return bool|null
     */
    protected function hasCertificates(ForgeClient $client)
    {
        try {
            return $client->certificates($this->site)->isNotEmpty();
        } catch (\Throwable $exception) {
            return $this->site->has_certificates;
        }
    }
}
