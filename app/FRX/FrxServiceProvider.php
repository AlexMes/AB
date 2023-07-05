<?php

namespace App\FRX;

use App\CRM\LeadOrderAssignment;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FrxServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap application services
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
    }

    /**
     * Register module routes
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::bind('frx_assignment', function ($value) {
            return LeadOrderAssignment::where('frx_lead_id', $value)
                ->whereHas(
                    'route',
                    fn ($query) => $query->whereIn('manager_id', auth('tenant')->user()->managers->pluck('id'))
                )
                ->firstOrFail();
        });

        Route::group([
            'domain'     => config('crm.domain'),
            'as'         => 'crm.frx.',
            'prefix'     => 'frx',
            'middleware' => [
                'auth:tenant',
                'throttle:700,1,\'frx\'',
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ],
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        });
    }
}
