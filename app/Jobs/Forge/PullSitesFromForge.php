<?php

namespace App\Jobs\Forge;

use App\Server;
use App\Services\Forge\ForgeClient;
use App\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PullSitesFromForge implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Server $server;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
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
        try {
            $client->sites($this->server)
                ->map(fn ($site)       => $this->process($site))
                ->each(fn (Site $site) => GetSiteCertificateStatus::dispatch($site));
        } catch (\Throwable $exception) {
            // any ideas?
        }
    }

    /**
     * Cache forge site
     *
     * @param $site
     *
     * @return Site
     */
    protected function process($site)
    {
        return Site::updateOrCreate(['forge_id' => $site['id']], array_merge(
            $site,
            [
                'server_id' => $this->server->id,
            ]
        ));
    }
}
