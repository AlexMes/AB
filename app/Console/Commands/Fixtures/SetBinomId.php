<?php

namespace App\Console\Commands\Fixtures;

use App\Binom\Campaign;
use App\Binom\Click;
use App\Binom\Statistic;
use App\Binom\TrafficSource;
use Illuminate\Console\Command;

class SetBinomId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:set-binom-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set id of 1 to all binom relatives, where binom id is null';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->task('Setting ID on traffic sources', function () {
            TrafficSource::whereNull('binom_id')->update(['binom_id' => 1]);
        });
        $this->task('Setting ID on campaigns', function () {
            Campaign::whereNull('binom_id')->update(['binom_id' => 1]);
        });
        $this->task('Setting ID on clicks', function () {
            Click::whereNull('binom_id')->update(['binom_id' => 1]);
        });
        $this->task('Setting ID on stats', function () {
            Statistic::whereNull('binom_id')->update(['binom_id' => 1]);
        });
    }
}
