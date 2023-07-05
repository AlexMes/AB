<?php

namespace App\Deluge\Http\Controllers;

use App\Deluge\Domain;
use App\Http\Controllers\Controller;

class CheckAllDomains extends Controller
{
    public function __invoke()
    {
        Domain::visible()->each(fn (Domain $domain) => $domain->check());

        return redirect()->back();
    }
}
