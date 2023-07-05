<?php

namespace App\Http\Controllers;

use App\Http\Requests\Managers\ResetPassword;
use App\Manager;

class ResetManagerPassword extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Manager                                   $manager
     * @param \App\Http\Requests\Managers\ResetPassword $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(Manager $manager, ResetPassword $request)
    {
        return response()->json(
            tap($manager)->update(['password' => $request->validated()['password']]),
            202
        );
    }
}
