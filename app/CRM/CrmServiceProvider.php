<?php

namespace App\CRM;

use App\CRM\Concenrs\EventMap;
use App\CRM\Policies\AssignmentPolicy;
use App\CRM\Policies\CallbackPolicy;
use App\CRM\Policies\LabelPolicy;
use App\CRM\View\Components\LeadComponent;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CrmServiceProvider extends ServiceProvider
{
    use EventMap;

    /**
     * Bootstrap application services
     *
     * @return void
     */
    public function boot()
    {
        $this->registerEvents();
        $this->registerRoutes();
        $this->registerPolicies();
        $this->registerResources();
        $this->registerPublishableFiles();
        $this->registerTranslations();
    }

    /**
     * Register module routes
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'domain'     => config('crm.domain'),
            'as'         => 'crm.',
            'middleware' => 'web',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        });
    }

    /**
     * Register module resources
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'crm');
        $this->loadViewComponentsAs('crm', [
            'lead' => LeadComponent::class,
        ]);
    }

    /**
     * Bindings to publishable files such
     * as configs, assets, etc.
     *
     * @return void
     */
    protected function registerPublishableFiles()
    {
        $this->publishes([
            __DIR__ . '/config/crm.php' => config_path('crm.php')
        ]);

        $this->publishes([
            __DIR__ . '/resources/sounds' => public_path('crm/sounds'),
        ], 'crm-assets');
    }

    /**
     * Bind policies for module
     *
     * @return void
     */
    protected function registerPolicies()
    {
        Gate::policy(LeadOrderAssignment::class, AssignmentPolicy::class);
        Gate::policy(Callback::class, CallbackPolicy::class);
        Gate::policy(Label::class, LabelPolicy::class);
    }

    /**
     * Register module translations
     *
     * @return void
     */
    protected function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'crm');
    }
}
