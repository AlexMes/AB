<?php

Route::redirect('/', '/login');
Auth::routes(['register' => false]);

Route::view('/policy', 'pages.policy')->name('policy');
Route::view('/terms', 'pages.terms')->name('terms');

Route::options('/external/lead', 'RespondToCors');
Route::options('/external/lead-without-phone', 'RespondToCors');
Route::post('/external/lead', 'External\StoreExternalLead')
    ->name('leads.external');
Route::post('/external/lead-without-phone', 'External\StoreExternalWithoutPhoneLead')
    ->name('leads.external-without-phone');
Route::get('/external/leads', 'External\ListLeads');
Route::post('/hooks/b24', 'ReceiveIncomingLead')->name('hook.b24');
Route::post('/hooks/zvonobot', 'ZvonobotReceiveIncomingLead')->name('hook.zvonobot');
Route::post('/hooks/marquiz', 'MarquizReceiveIncomingLead')->name('hook.marquiz');

Route::match(['get', 'post'], '/telegram/webhook', 'HandleTelegramWebhook')
    ->name('telegram.webhook');

Route::get('google-tfa', 'Auth\LoginController@showTFAForm')->name('google-tfa.login');
Route::post('google-tfa', 'Auth\LoginController@loginTFA');

Route::middleware([
    'auth',
    'firewall',
    'tfa',
    'grant-privileges:web',
    \App\Http\Middleware\AuthorizedToAccessBoard::class,
])->group(static function () {
    Route::get('/logout', 'Auth\LoginController@logout')->withoutMiddleware('tfa');
    Route::get('/{any?}', 'RenderApplication')
        ->where('any', '[\/\w\.-]*')
        ->name('app');
});
