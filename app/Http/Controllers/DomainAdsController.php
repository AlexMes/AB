<?php

namespace App\Http\Controllers;

use App\Domain;
use Illuminate\Http\Request;

class DomainAdsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Domain $domain)
    {
        $this->authorize('view', $domain);

        return $domain->ads;
    }
}
