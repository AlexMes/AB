<?php

namespace App\Http\Controllers\Sms;

use App\Enums\SmsService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     *@throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     *
     */
    public function __invoke(Request $request)
    {
        return response()->json(collect(SmsService::DRIVERS)->values());
    }
}
