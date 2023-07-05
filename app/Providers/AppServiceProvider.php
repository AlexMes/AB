<?php

namespace App\Providers;

use App;
use App\Facebook\Account;
use App\Facebook\Ad;
use App\Facebook\AdSet;
use App\Facebook\Campaign;
use App\Facebook\Profile;
use App\Trail;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // temporary solution
        $this->app->singleton(Trail::class);
        //todo: drop this shit
        App::terminating(fn () => app(Trail::class)->send());

        Carbon::setLocale('ru_RU');
        HeadingRowFormatter::default('none');
        Relation::morphMap([
            'profiles'    => Profile::class,
            'accounts'    => Account::class,
            'adset'       => AdSet::class,
            'ad'          => Ad::class,
            'campaign'    => Campaign::class
        ]);
    }
}
