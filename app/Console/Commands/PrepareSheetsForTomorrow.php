<?php

namespace App\Console\Commands;

use App\LeadOrderRoute;
use App\LeadsOrder;
use Illuminate\Console\Command;

class PrepareSheetsForTomorrow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:prepare-for-next-day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare docs and sheets for next day';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = today()->addDay();

        LeadOrderRoute::whereIn('order_id', LeadsOrder::where('date', $date)->pluck('id'))
            ->each(function (LeadOrderRoute $route) use ($date) {
                $this->info('Working on '.$route->manager->name);
                if (! $route->manager->spreadsheet()->hasSheet(sprintf(
                    '%s-%s',
                    $date->monthName,
                    $route->offer->name,
                ))) {
                    try {
                        $route->manager->spreadsheet()->createSheet(sprintf(
                            '%s-%s',
                            $date->monthName,
                            $route->offer->name,
                        ));
                    } catch (\Throwable $th) {
                        $this->info('Failure');
                    }
                }

                sleep(20);
            });
    }
}
