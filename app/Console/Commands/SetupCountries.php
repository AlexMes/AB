<?php

namespace App\Console\Commands;

use App\Country;
use App\IpAddress;
use Illuminate\Console\Command;

class SetupCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $countries = IpAddress::whereNotNull('country_code')->distinct('country_code')->get(['country_code', 'country_name']);

        $countries->each(function (IpAddress $country) {
            if (! Country::where('code', $country->country_code)->exists()) {
                $c = Country::create([
                    'code' => $country->country_code,
                    'name' => $country->country_name
                ]);

                $this->info('Created '.$c->name);
            }
        });

        return 0;
    }
}
