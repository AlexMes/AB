<?php

namespace App\Binom\Http\Controllers;

use App\Binom\Binom;
use App\Http\Controllers\Controller;

class Landings extends Controller
{
    /**
     * Binom "SDK" instance
     *
     * @var \App\Binom\Binom
     */
    private $binom;

    /**
     * Landings constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->binom = app('binom');
    }

    /**
     * Load all landings
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        return response()->json($this->binom->getLandings(), 200);
    }
}
