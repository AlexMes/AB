<?php

Route::post('accounts', 'Accounts')->name('accounts.store');
Route::post('campaigns', 'Campaigns')->name('campaigns.store');
Route::post('adsets', 'AdSets')->name('adsets.store');
Route::post('ads', 'Ads')->name('ads.store');
Route::post('pages', 'Pages')->name('pages.store');
Route::post('insights', 'Insights')->name('insights.store');
