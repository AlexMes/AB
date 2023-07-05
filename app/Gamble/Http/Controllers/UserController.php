<?php

namespace App\Gamble\Http\Controllers;

use App\Gamble\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return response()->json(User::all());
    }
}
