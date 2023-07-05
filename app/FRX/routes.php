<?php

use App\FRX\Http\Controllers;

Route::put('/leads/{frx_assignment}', Controllers\Assignments::class)->name('assignments.update');
Route::apiResource('/leads/{frx_assignment}/callbacks', Controllers\Callbacks::class)->only('store');
Route::post('/leads/{frx_assignment}/callbacks/mark-called', Controllers\MarkCallbackCalled::class)
    ->name('callbacks.mark-called');
