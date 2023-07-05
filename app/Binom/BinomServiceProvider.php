<?php

namespace App\Binom;

use App\Binom\Commands\CacheCampaigns;
use App\Binom\Commands\CacheCampaignStats;
use App\Binom\Commands\CacheTrafficSources;
use App\Binom\Commands\CheckStatistics;
use App\Binom\Commands\TimeReport;
use App\Binom\Http\Controllers\Campaigns;
use App\Binom\Http\Controllers\Landings;
use App\Binom\Http\Controllers\Offers;
use App\Binom\Policies\CampaignPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BinomServiceProvider extends ServiceProvider
{
    /**
     * List of model access bindings
     *
     * @var array
     */
    protected $policies = [
        Campaign::class    => CampaignPolicy::class,
    ];

    /**
     * Register application services
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('binom', function ($app) {
            return new Binom(
                $app['config']['services']['binom']['url'],
                $app['config']['services']['binom']['token']
            );
        });
    }

    /**
     * Bootstrap application services
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerRoutes();
        $this->registerCommands();
    }

    /**
     * Register module routes
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::middleware(['auth:api','firewall'])->group(function () {
            Route::get('/api/binom/offers', Offers::class)->name('binom.offers');
            Route::get('/api/binom/landings', Landings::class)->name('binom.landings');
            Route::get('/api/binom/campaigns', Campaigns::class)->name('binom.campaigns');
        });
    }

    /**
     * Register module commands
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands([
            CacheCampaigns::class,
            CacheCampaignStats::class,
            TimeReport::class,
            CheckStatistics::class,
            CacheTrafficSources::class,
        ]);
    }

    /**
     * Register module policies
     *
     * @return void
     */
    protected function registerPolicies()
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
