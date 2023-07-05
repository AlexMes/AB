<?php

namespace App\Console;

use App\AdsBoard;
use App\Binom\Commands\CacheCampaigns;
use App\Binom\Commands\CacheCampaignStats;
use App\Console\Commands\AutoDeleteDuplicateAssignments;
use App\Console\Commands\AutomaticDestinationDeactivate;
use App\Console\Commands\CheckDomainsStatus;
use App\Console\Commands\CheckManualAppStatus;
use App\Console\Commands\CheckMessagesDeliveryStatus;
use App\Console\Commands\CheckUpcomingSubscriptions;
use App\Console\Commands\CollectLeadsForBatch;
use App\Console\Commands\CollectLeadsForDuplicates;
use App\Console\Commands\CollectResults;
use App\Console\Commands\DeliverDelayedAssignments;
use App\Console\Commands\DispatchLeadsDistribution;
use App\Console\Commands\DispatchOldLeadsDistribution;
use App\Console\Commands\Fixture\AttachMissingBuyersLeads;
use App\Console\Commands\Fixtures\AdjustResultsWithOrders;
use App\Console\Commands\Fixtures\FixAssignmentsNumbers;
use App\Console\Commands\FreshAmoCrmTokens;
use App\Console\Commands\MakeAssignmentDTDSnapshot;
use App\Console\Commands\NotifyManagerAboutCallback;
use App\Console\Commands\Orders\LookupOverdueOrders;
use App\Console\Commands\PauseLeadOrders;
use App\Console\Commands\Postbacks\PullAffclubResults;
use App\Console\Commands\Postbacks\PullAffiliates360Results;
use App\Console\Commands\Postbacks\PullInvestexResults;
use App\Console\Commands\Postbacks\PullOptimusResults;
use App\Console\Commands\Postbacks\PullPlatform500Results;
use App\Console\Commands\Postbacks\PullProfitCenterResults;
use App\Console\Commands\Postbacks\PullSupremeResults;
use App\Console\Commands\Postbacks\PullTrafficDandyResults;
use App\Console\Commands\PullLeadsInfoFromSheets;
use App\Console\Commands\PullTrackBoxResults;
use App\Console\Commands\ReplaceAssignmentLead;
use App\Console\Commands\Results\PullForexCatsResults;
use App\Console\Commands\SaveInitialPlan;
use App\Console\Commands\SetAssignmentsStatusesCommand;
use App\Console\Commands\SetupCountries;
use App\Console\Commands\SetUserToLeadsAndDeps;
use App\Console\Commands\StartBranchBatch;
use App\Console\Commands\StartLeadOrders;
use App\Deluge\Console\CheckDomains;
use App\LeadDestinationDriver;
use App\Unity\Commands\Cache;
use App\VK\Commands\Refresh;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Laravel\Horizon\Console\SnapshotCommand;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(AutomaticDestinationDeactivate::class)->dailyAt('07:00');
        $schedule->command(DeliverDelayedAssignments::class)->everyMinute();
        // $schedule->command(ReplaceAssignmentLead::class)->everyMinute();
        $schedule->command(StartLeadOrders::class)->everyMinute();
        $schedule->command(PauseLeadOrders::class)->everyMinute();
        $schedule->command(CheckUpcomingSubscriptions::class)->dailyAt('08:00');
        $schedule->command(FixAssignmentsNumbers::class)->dailyAt('04:10');
        $schedule->command(AdjustResultsWithOrders::class)->dailyAt('04:30');
        $schedule->command(DispatchOldLeadsDistribution::class)->dailyAt('07:00');
        $schedule->command(DispatchLeadsDistribution::class)->everyTenMinutes()->between('07:00', '10:01');
        $schedule->command(CheckDomainsStatus::class)->everyFifteenMinutes();
        // $schedule->command(LookupOverdueOrders::class)->everyFifteenMinutes();
        $schedule->command(CacheCampaigns::class)->everyFiveMinutes();
        $schedule->command(CacheCampaignStats::class)->everyFiveMinutes();
        $schedule->command(CheckMessagesDeliveryStatus::class)->hourly();
        $schedule->command(SnapshotCommand::class)->everyFiveMinutes();
        $schedule->command(PullLeadsInfoFromSheets::class)->dailyAt('21:00');
        // $schedule->command(SaveInitialPlan::class)->dailyAt('03:00');
        $schedule->command(NotifyManagerAboutCallback::class)->everyMinute();
        $schedule->command(PullTrackBoxResults::class)->hourly()->runInBackground()->then(function () {
            AdsBoard::report('Collected ftds from the trackbox');
        });
        $schedule->command(PullAffiliates360Results::class)->hourly()->runInBackground()->then(function () {
            AdsBoard::report('Collected ftds from the 360');
        });
        $schedule->command(PullAffclubResults::class)->hourly()->runInBackground()->then(function () {
            AdsBoard::report('Collected ftds from the affclub');
        });
        $schedule->command(PullInvestexResults::class)->hourly()->runInBackground()->then(function () {
            AdsBoard::report('Collected ftds from the investex');
        });
        $schedule->command(PullForexCatsResults::class)->hourly()->runInBackground()->then(function () {
            AdsBoard::report('Collected ftds from the forex cats');
        });
        $schedule->command(PullTrafficDandyResults::class)->hourly()->runInBackground()->then(function () {
            AdsBoard::report('Collected ftds from the traffic dandy');
        });
        $schedule->command(PullSupremeResults::class)->hourly()->runInBackground()->then(function () {
            AdsBoard::report('Collected ftds from the supreme');
        });
        $schedule->command(PullProfitCenterResults::class)->hourly()->runInBackground()->then(function () {
            AdsBoard::report('Collected ftds from the pcfx');
        });
        $schedule->command(PullPlatform500Results::class)->hourly()->runInBackground()->then(function () {
            AdsBoard::report('Collected ftds from the platform500');
        });
        $schedule->command(PullOptimusResults::class)->hourly()->runInBackground()->then(function () {
            AdsBoard::report('Collected ftds from the Optimus');
        });
        $schedule->command(SetAssignmentsStatusesCommand::class)->dailyAt('03:00');
        $schedule->command(MakeAssignmentDTDSnapshot::class)->dailyAt('00:00');
        $schedule->command(AttachMissingBuyersLeads::class)->everyFifteenMinutes();
        $schedule->command(CollectResults::class, [now()->subDays(30)->toDateString()])->everyTwoHours(15);
        $schedule->command(CollectResults::class)->dailyAt('02:00');
        // $schedule->command(CollectResults::class, [now()->subDay()->toDateString(), '--driver' => LeadDestinationDriver::ONEXCPA])->hourlyAt(10)->unlessBetween('02:00', '02:11');
        $schedule->command(CollectResults::class, [now()->subDay()->toDateString(), '--driver' => LeadDestinationDriver::GLOBALALLIANCE])
            ->hourlyAt(15)->unlessBetween('02:00', '02:16');
        $schedule->command(CollectResults::class, [now()->subDay()->toDateString(), '--driver' => LeadDestinationDriver::OPTIMUS])
            ->hourlyAt(20)->unlessBetween('02:00', '02:21');
        // $schedule->command(FreshAmoCrmTokens::class)->everyTenMinutes();
        $schedule->command(CheckDomains::class)->everyTenMinutes();
        $schedule->command(SetupCountries::class)->everyFourHours();

        // $schedule->command(CollectLeadsForBatch::class)->dailyAt('00:00');
        $schedule->command(CollectLeadsForDuplicates::class)->dailyAt('06:00');
        $schedule->command(CollectLeadsForDuplicates::class, ['--branch_id' => 16])->dailyAt('06:00');
        $schedule->command(StartBranchBatch::class)->dailyAt('10:00');
        // $schedule->command(CheckManualAppStatus::class)->everyFifteenMinutes();
        $schedule->command(SetUserToLeadsAndDeps::class, ['--user_id' => 233, '--branch_id' => 19])->dailyAt('05:00');
        $schedule->command(Refresh::class)->everyFifteenMinutes();
        $schedule->command(Cache::class, ['--since' => now()->subDay()])->hourlyAt(10);
        $schedule->command(\App\Unity\Commands\Refresh::class)->everyFifteenMinutes();
        $schedule->command(AutoDeleteDuplicateAssignments::class)->everyTenMinutes()->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }
}
