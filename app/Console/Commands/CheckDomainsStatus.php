<?php

namespace App\Console\Commands;

use App\Domain;
use Illuminate\Console\Command;

class CheckDomainsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check domain is up or not';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $domains = Domain::query()
            ->where(
                fn ($q) => $q->whereHas('leads', fn ($query) => $query->where('leads.created_at', '>=', now()->subMonth()->toDateTimeString()))
                    ->orWhere('domains.created_at', '>=', now()->subWeek())
            )
            ->whereIn('linkType', [Domain::SERVICE,Domain::LANDING])
            ->ready();

        foreach ($domains->cursor() as $domain) {
            /** @var \App\Domain $domain */
            $domain->check();
        }

        $this->info('Domains checks dispatched');
    }
}
