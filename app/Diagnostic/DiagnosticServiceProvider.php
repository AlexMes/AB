<?php

namespace App\Diagnostic;

use App\Diagnostic\Http\Controllers\ApplicationRoles;
use App\Diagnostic\Http\Middleware\DevelopersOnly;
use App\Facebook\Commands\Refresh;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Class DiagnosticServiceProvider
 *
 * @package App\Diagnostic
 */
class DiagnosticServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap application services
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerCommands();
    }

    /**
     * Register service routes
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::prefix('diagnostics')
            ->middleware(['auth:api', DevelopersOnly::class])->group(function () {
                Route::get('/application/roles', ApplicationRoles::class)->name('diagnostics.roles');
            });
    }

    /**
     * Register module commands
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Refresh::class,
            ]);
        }
    }
}
