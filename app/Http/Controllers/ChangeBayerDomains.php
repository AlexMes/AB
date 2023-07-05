<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Http\Requests\Domains\ChangeBayerDomains as ChangeBayerDomainsRequest;

class ChangeBayerDomains extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param ChangeBayerDomainsRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ChangeBayerDomainsRequest $request)
    {
        Domain::whereIn('id', $request->input('domain_ids'))
            ->update(['user_id' => $request->input('user_id')]);

        return response()->noContent();
    }
}
