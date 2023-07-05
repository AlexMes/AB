<?php

namespace App\Deluge\Console;

use App\Deluge\Domain;
use Illuminate\Console\Command;

class CheckDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deluge:domains:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check domains state';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Domain::each(function (Domain $domain) {
            $domain->check();
        });

        return 0;
    }
}
