<?php

namespace App\Facebook\Commands;

use App\Facebook\Profile;
use Illuminate\Console\Command;

class Refresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fb:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh connected Facebook profiles';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Profile::issueDoesntExist()
            ->each(function (Profile $profile) {
                $profile->refreshFacebookData();
            });
    }
}
