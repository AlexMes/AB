<?php

use App\Leads\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::options('register', \App\Http\Controllers\RespondToCors::class);
Route::post('register', Controllers\RegisterLead::class)->name('register');
