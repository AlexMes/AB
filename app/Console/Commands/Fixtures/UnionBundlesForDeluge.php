<?php

namespace App\Console\Commands\Fixtures;

use App\ManualBundle;
use App\ManualCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UnionBundlesForDeluge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:union-bundles-for-deluge';

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
        $this->task('Fixing 2-7', function () {
            DB::transaction(function () {
                ManualCampaign::whereBundleId(2)->update(['bundle_id' => 7]);
                ManualBundle::find(2)->delete();
            });
        });

        $this->task('Fixing 11-13', function () {
            DB::transaction(function () {
                ManualCampaign::whereBundleId(11)->update(['bundle_id' => 13]);
                ManualBundle::find(11)->delete();
            });
        });

        $this->task('Fixing 14-18', function () {
            DB::transaction(function () {
                ManualCampaign::whereBundleId(14)->update(['bundle_id' => 18]);
                ManualBundle::find(14)->delete();
            });
        });

        $this->task('Fixing 27-29', function () {
            DB::transaction(function () {
                ManualCampaign::whereBundleId(27)->update(['bundle_id' => 29]);
                ManualBundle::find(27)->delete();
            });
        });

        $this->task('Fixing 64-65', function () {
            DB::transaction(function () {
                ManualCampaign::whereBundleId(64)->update(['bundle_id' => 65]);
                ManualBundle::find(64)->delete();
            });
        });

        return 0;
    }
}
