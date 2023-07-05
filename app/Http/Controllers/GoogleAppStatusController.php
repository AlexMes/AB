<?php

namespace App\Http\Controllers;

use App\GoogleApp;

class GoogleAppStatusController extends Controller
{
    /**
     * GoogleAppStatusController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(GoogleApp::class, 'app');
    }

    /**
     * Enable in-app web view
     *
     * @param \App\GoogleApp $app
     *
     * @return bool
     */
    public function store(GoogleApp $app)
    {
        return $app->enable();
    }

    /**
     * Disable in-app web view
     *
     * @param \App\GoogleApp $app
     *
     * @return bool
     */
    public function destroy(GoogleApp $app)
    {
        return $app->disable();
    }
}
