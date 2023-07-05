<?php

namespace App\Gamble;

use App\Gamble\Http\Controllers\SendEventToFacebook;
use App\Gamble\Http\Middleware\AuthorizedToAccessGamble;
use App\Gamble\Policies\AccountPolicy;
use App\Gamble\Policies\CampaignPolicy;
use App\Gamble\Policies\GoogleAppLinkPolicy;
use App\Gamble\Policies\GoogleAppPolicy;
use App\Gamble\Policies\GroupPolicy;
use App\Gamble\Policies\InsightPolicy;
use App\Gamble\Policies\OfferPolicy;
use App\Gamble\Policies\TechCostPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class GambleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap application services
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerPolicies();
        $this->registerResources();
    }

    /**
     * Register module resources
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'gamble');
    }

    /**
     * Register module routes
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::domain(config('gamble.domain'))
            ->middleware('web')
            ->get('/api/facebook/events', SendEventToFacebook::class)
            ->name('gamble.api.facebook.event');

        Route::domain(config('gamble.domain'))
            ->as('gamble.api.')
            ->prefix('api')
            ->middleware(['api', 'auth:api', AuthorizedToAccessGamble::class])
            ->namespace('App\Gamble\Http\Controllers')
            ->group(fn () => $this->loadRoutesFrom(__DIR__ . '/routes/api.php'));

        Route::group([
            'domain'     => config('gamble.domain'),
            'as'         => 'gamble.',
            'middleware' => 'web',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        });
    }

    /**
     * Bind policies for module
     *
     * @return void
     */
    protected function registerPolicies()
    {
        Gate::policy(GoogleApp::class, GoogleAppPolicy::class);
        Gate::policy(GoogleAppLink::class, GoogleAppLinkPolicy::class);
        Gate::policy(Account::class, AccountPolicy::class);
        Gate::policy(Campaign::class, CampaignPolicy::class);
        Gate::policy(Insight::class, InsightPolicy::class);
        Gate::policy(Offer::class, OfferPolicy::class);
        Gate::policy(Group::class, GroupPolicy::class);
        Gate::policy(TechCost::class, TechCostPolicy::class);
    }
}
