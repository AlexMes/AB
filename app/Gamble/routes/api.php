<?php

Route::apiResource('applications', 'Applications\Applications');
Route::apiResource('applications.links', 'Applications\Links')->except(['show']);

Route::get('/countries', 'CountryController');
Route::get('/users', 'UserController');

Route::apiResource('accounts', 'Accounts\Accounts')->except(['destroy']);
Route::apiResource('campaigns', 'Campaigns\Campaigns')->except(['destroy']);
Route::apiResource('insights', 'Insights')->except(['destroy']);
Route::apiResource('tech-costs', 'TechCostController')->except(['destroy']);

Route::apiResource('offers', 'Offers')->except(['destroy']);
Route::apiResource('groups', 'Groups')->except(['destroy']);

Route::prefix('reports')->namespace('Reports')->as('reports.')->group(static function () {
    Route::get('/performance', 'Performance')->name('performance');
});
