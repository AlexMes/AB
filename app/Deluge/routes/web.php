<?php

use App\Deluge\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::get('/login', Controllers\ShowLoginPage::class)->name('login');
Route::post('/login', Controllers\Authenticate::class)->name('authenticate');

Route::get('google-tfa', Controllers\Authenticate::class . '@showTFAForm')->name('google-tfa.login');
Route::post('google-tfa', Controllers\Authenticate::class . '@loginTFA');

Route::middleware([
    'auth:web',
    'tfa',
    \App\Deluge\Http\Middlewares\AuthorizedToAccessDeluge::class,
    \App\Deluge\Http\Middlewares\CheckOffice::class,
])->group(function () {
    Route::get('/logout', Controllers\Logout::class)->name('logout')->withoutMiddleware('tfa');
    Route::resource('accounts', Controllers\Accounts\Accounts::class);
    Route::resource('accounts.campaigns', Controllers\Accounts\Campaigns::class)
        ->only(['index', 'store', 'create']);
    Route::post('/imports/insights', Controllers\ImportInsights::class)->name('imports.insights');

    Route::put('/account-pour/{pivot}', Controllers\AccountPour::class)->name('account-pour.update');
    Route::resource('bundles', Controllers\Bundles::class);
    Route::resource('campaigns', Controllers\Campaigns\Campaigns::class);
    Route::resource('campaigns.insights', Controllers\Campaigns\Insights::class)
        ->only(['index', 'store', 'create']);
    Route::resource('insights', Controllers\Insights::class);
    Route::resource('groups', Controllers\Groups::class);
    Route::resource('pours', Controllers\Pours::class)->only(['index', 'show', 'destroy']);
    Route::post('accounts-pours', Controllers\AccountsPours::class)->name('accounts-pours');
    Route::resource('credit-cards', Controllers\CreditCards::class);
    Route::resource('suppliers', Controllers\Suppliers::class);
    Route::resource('apps', Controllers\Apps::class);
    Route::resource('traffic-sources', Controllers\TrafficSources::class);
    Route::as('unity.')->prefix('unity')->group(static function () {
        Route::resource('organizations', Controllers\Unity\Organizations::class);
        Route::resource('apps', Controllers\Unity\Apps::class);
        Route::resource('campaigns', Controllers\Unity\Campaigns::class);
        Route::resource('insights', Controllers\Unity\Insights::class);
        Route::post('/organizations/{organization}/refresh', Controllers\Unity\RefreshOrganization::class)
            ->name('organizations.refresh');
    });

    Route::get('/reports/performance', Controllers\Reports\Performance::class)->name('reports.performance');
    Route::get('/reports/quiz', Controllers\Reports\Quiz::class)->name('reports.quiz');
    Route::get('/reports/account-stats', Controllers\Reports\AccountStats::class)->name('reports.account-stats');
    Route::get('/reports/buyer-stats', Controllers\Reports\BuyerStats::class)->name('reports.buyer-stats');
    Route::get('/reports/designer-conversion', Controllers\Reports\DesignerConversion::class)
        ->name('reports.designer-conversion');
    Route::get('/reports/average-spend', Controllers\Reports\AverageSpend::class)->name('reports.average-spend');
    Route::get('/reports/buyer-costs', Controllers\Reports\BuyerCosts::class)->name('reports.buyer-costs');
    Route::get('/reports/lead-stats', Controllers\Reports\LeadStats::class)->name('reports.lead-stats');
    Route::get('/reports/exports/buyer-costs', Controllers\Reports\Exports\BuyerCosts::class)
        ->name('reports.exports.buyer-costs');
    Route::get('/reports/exports/quiz', Controllers\Reports\Exports\Quiz::class)
        ->name('reports.exports.quiz');
    Route::get('/reports/exports/performance', Controllers\Reports\Exports\Performance::class)
        ->name('reports.exports.performance');

    Route::resource('domains', Controllers\DomainsController::class)->except('destroy');
    Route::post('/domains/check', Controllers\CheckDomain::class)->name('domains.check.all');
    Route::post('/domains/{domain}/check', Controllers\CheckDomain::class)->name('domains.check');

    Route::get('/leads', Controllers\LeadsController::class)->name('leads.index');

    Route::post('/revenue-visibility', Controllers\RevenueVisibility::class)->name('revenue-visibility');

    Route::redirect('/{any?}', route('deluge.accounts.index'))->where(['any' => '.*']);
});
