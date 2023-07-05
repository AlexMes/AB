<?php

namespace App\Http\Controllers\Enums;

use App\Http\Controllers\Controller;
use App\Office;
use Illuminate\Http\Request;

class Offices extends Controller
{
    /**
     * Get all offices
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function __invoke(Request $request)
    {
        return Office::visible()->get(['id','name']);
    }
}
