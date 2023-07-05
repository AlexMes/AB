<?php

namespace App\FRX\Http\Controllers;

use App\CRM\LeadOrderAssignment;
use App\FRX\Http\Requests\UpdateAssignment;
use App\Http\Controllers\Controller;

class Assignments extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param UpdateAssignment    $request
     * @param LeadOrderAssignment $frxAssignment
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UpdateAssignment $request, LeadOrderAssignment $frxAssignment)
    {
        $frxAssignment->update($request->validated());

        return response()->noContent(202);
    }
}
