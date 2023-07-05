<?php

namespace App\Binom\Http\Controllers;

use App\Binom\Binom;
use App\Http\Controllers\Controller;

class Offers extends Controller
{
    /**
     * @var \App\Binom\Binom
     */
    private $binom;

    /**
     * Offers constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->binom = app('binom');
    }

    /**
     * Get all Binom landings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        return response()->json($this->binom->getOffers(), 200);
    }
}
