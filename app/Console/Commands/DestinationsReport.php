<?php

namespace App\Console\Commands;

use App\DestinationDrivers\CallResult;
use App\DestinationDrivers\Contracts\CollectsCallResults;
use App\Jobs\ProcessCallResults;
use App\LeadDestination;
use App\LeadDestinationDriver;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class DestinationsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'destinations:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate report for all destinations in given period';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $since = Carbon::parse($this->ask('since') ?? now()->startOfMonth())->startOfDay();

        $destinations = LeadDestination::whereHas('assignments', fn ($query) => $query->whereBetween('created_at', [$since, now()->endOfDay()]))
            ->where('driver', '!=', LeadDestinationDriver::FOREXCATS)
            ->where('id', '>', '3')
            ->get();

        $this->info(sprintf('Gotta walk through %s destinations', $destinations->count()));

        $report = $destinations->map(function (LeadDestination $destination, $key) use ($since, $destinations) {
            $this->info(sprintf('[%s/%s] Working on destination %s', $key, $destinations->count(), $destination->name));
            $this->pull($destination, $since);
        });

        Storage::put('destinations-report-'.now()->timestamp.'.html', view('reports.destinations', [
            'since'  => $since,
            'report' => $report,
        ]));

        return 0;
    }

    protected function pull(LeadDestination $destination, Carbon $since)
    {
        $externals = $destination->assignments()->where('created_at', '>=', $since->toDateTimeString())->pluck('external_id');

        $error        = null;
        $leads        = 0;
        $deposits     = 0;
        $missingLeads = collect();
        $missingFtds  = collect();
        $page         = 0;


        $implementation = $destination->initialize();
        if (! $implementation instanceof CollectsCallResults) {
            $error = 'No implementation for results collection';
        } else {
            do {
                try {
                    $results = $implementation->pullResults(Carbon::parse($since), $page++);
                    $leads += count($results);
                    $missingLeads->merge($results->reject(fn (CallResult $result) => $externals->contains($result->getId()))->map(fn (CallResult $result) => $result->getId()));
                    $missingFtds->merge($results->filter(fn (CallResult $result) => $result->isDeposit())->reject(fn (CallResult $result) => $externals->contains($result->getId()))->map(fn (CallResult $result) => $result->getId()));
                    $deposits += $results->reduce(fn ($carry, $result) => $carry + ($result->isDeposit() ? 1 : 0), 0);
                    $this->warn($destination->name.' Processing '. $results->count(). 'results. Page:'.$page.'  Since '.Carbon::parse($since)->toDateTimeString());
                    $this->warn('Current. leads '. $leads. ' Missing:'.$missingLeads->count().'  missing ftds '.$missingFtds->count().' Total ftds '.$deposits);

                    ProcessCallResults::dispatchNow($destination, $results);
                } catch (\Throwable $th) {
                    $results = collect();
                    $error   = $th->getMessage();
                }
            } while ($results->count() !== 0 && is_null($error) && $page < 500);
        }

        return [
            'destination'  => $destination->name,
            'branch'       => optional($destination->branch)->name,
            'pages'        => $page - 1,
            'assignments'  => $externals->count(),
            'leads'        => $leads,
            'deposits'     => $deposits,
            'missingLeads' => $missingLeads,
            'missingFtd'   => $missingFtds,
            'error'        => $error,
        ];
    }
}
