<?php

namespace App\VK\Commands;

use App\VK\Models\Profile;
use Illuminate\Console\Command;

class Refresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vk:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh connected VK profiles';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Profile::withoutIssues()
            ->each(function (Profile $profile) {
                $profile->refreshVKData();
            });
    }
}
