<?php

namespace App\Console\Commands\Fixtures;

use App\CRM\CrmServiceProvider;
use Illuminate\Console\Command;

class PublishCrmAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:publish-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes crm assets.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--provider' => CrmServiceProvider::class,
            '--tag'      => 'crm-assets',
            '--force'    => true,
        ]);

        return 0;
    }
}
