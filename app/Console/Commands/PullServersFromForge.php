<?php

namespace App\Console\Commands;

use App\Server;
use App\Services\Forge\ForgeClient;
use Illuminate\Console\Command;
use Throwable;

class PullServersFromForge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forge:pull-servers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache servers registered on Forge';

    /**
     * Execute the console command.
     *
     * @param \App\Services\Forge\ForgeClient $client
     *
     * @return void
     */
    public function handle(ForgeClient $client)
    {
        try {
            $client->servers()->each(fn ($server) => $this->process($server));
        } catch (Throwable $e) {
            // Shit happens
        }
    }

    /**
     * Save or update server
     *
     * @param $server
     */
    protected function process($server)
    {
        Server::query()->updateOrCreate(['forge_id' => $server['id']], $server);
    }
}
