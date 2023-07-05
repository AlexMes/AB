<?php

use App\Gamble\Http\Controllers;

Route::redirect('/', '/login');
Route::get('/login', Controllers\Auth\ShowLoginPage::class)->name('login');
Route::post('/login', Controllers\Auth\Authenticate::class)->name('authenticate');


Route::group(['middleware' => ['auth:gamble', \App\Gamble\Http\Middleware\AuthorizedToAccessGamble::class]], static function () {
    Route::get('/logout', Controllers\Auth\Logout::class)->name('logout');
    Route::get('/{any?}', Controllers\RenderApplication::class)
        ->where('any', '[\/\w\.-]*')
        ->name('app');
});
