<?php

namespace App\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;

class DisplayQuizResults extends Controller
{
    /**
     * Display lead quiz results
     *
     * @param $uuid
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function __invoke($uuid, Request $request)
    {
        if ($request->hasValidSignature(true)) {
            return view('crm::quiz')->with([
                'poll' => Lead::firstWhere('uuid', $uuid)->pollResults(),
            ]);
        }

        return redirect('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    }
}
