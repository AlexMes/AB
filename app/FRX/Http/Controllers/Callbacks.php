<?php

namespace App\FRX\Http\Controllers;

use App\CRM\LeadOrderAssignment;
use App\FRX\Http\Requests\CreateCallback;
use App\Http\Controllers\Controller;

class Callbacks extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param CreateCallback      $request
     * @param LeadOrderAssignment $frxAssignment
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(CreateCallback $request, LeadOrderAssignment $frxAssignment)
    {
        $frxAssignment->actualCallback()
            ->fill($request->validated())
            ->save();

        return response()->noContent(202);
    }
}
