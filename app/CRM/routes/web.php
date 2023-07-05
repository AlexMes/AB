<?php

use App\CRM\Http\Controllers;

Route::redirect('/', '/login');
Route::get('/login', Controllers\ShowLoginPage::class);
Route::post('/login', Controllers\Authenticate::class)->name('authenticate');

Route::prefix('answers')->group(function () {
    Route::get('/{uuid}', Controllers\DisplayQuizResults::class)->name('quiz');
    Route::fallback(\App\Http\Controllers\Roll::class);
});

Route::domain(sprintf("{tenant}.%s", config('crm.domain')))->group(function () {
    Route::get('/login', Controllers\ShowLoginPage::class)->name('login');
    Route::get('/redirect', Controllers\RedirectToCrm::class)->name('auth.redirect');
    Route::get('/auth/callback', Controllers\AuthCallback::class)->name('auth.callback');
});

Route::middleware([
    'auth:web,crm',
    \App\CRM\Http\Middlewares\GrantExtraPrivileges::class,
    \App\CRM\Http\Middlewares\AuthorizedToAccessCrm::class,
    \App\CRM\Http\Middlewares\DetermineLocale::class,
])->group(function () {
    Route::get('/logout', Controllers\Logout::class)->name('logout');
    Route::resource('assignments', Controllers\Assignments::class);
    Route::get('/assignments/{assignment}/call', Controllers\StartCall::class)->name('assignment.call');
    Route::get('/assignments/{assignment}/call-alt', Controllers\StartCallToAlternative::class)->name('assignment.call-alt');
    Route::post('/assignments/{assignment}/transfer', Controllers\TransferAssignment::class)->name('assignment.transfer');
    Route::post('/assignments/transfer', Controllers\MassTransferAssignments::class)->name('assignments.mass-transfer');
    Route::post('/assignments/mass-delete', Controllers\MassDeleteAssignments::class)->name('assignments.mass-delete');
    Route::post('/assignments/mass-mark-leftover', Controllers\MassMarkLeftoverAssignments::class)
        ->name('assignments.mass-mark-leftover');
    Route::post('/assignments/{assignment}/mark-leftover', Controllers\MarkLeftoverAssignment::class)
        ->name('assignments.mark-leftover');
    Route::get('/statistic', Controllers\Statistic::class)->name('statistic');
    Route::get('/manager-statistic', Controllers\ManagerStatistic::class)->name('manager-statistic');
    Route::get('/office-statistic', Controllers\OfficeStatistic::class)->name('office-statistic');
    Route::get('/export/assignments', Controllers\Exports\Assignments::class)->name('export.assignments');
    Route::resource('labels', Controllers\Labels::class);
    Route::post('/managers/locale', Controllers\ManagerLocale::class)->name('managers.locale.update');
    Route::redirect('/{any?}', route('crm.assignments.index'))->where(['any' => '.*']);
});
