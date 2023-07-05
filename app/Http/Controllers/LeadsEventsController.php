<?php

namespace App\Http\Controllers;

use App\Lead;

class LeadsEventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function index(Lead $lead)
    {
        $this->authorize('view', $lead);

        return $lead->events()->with('auth')->orderBy('created_at')->get();
    }
}
