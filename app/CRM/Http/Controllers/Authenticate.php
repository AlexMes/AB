<?php

namespace App\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Authenticate extends Controller
{
    use AuthenticatesUsers;

    /**
     * Authenticate user with login and password
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        try {
            $response = $this->login($request);
        } catch (ValidationException $exception) {
            if (auth('crm')->attempt($this->credentials($request), $request->filled('remember'))) {
                return $this->sendLoginResponse($request);
            }

            return $this->sendFailedLoginResponse($request);
        }

        return $response;
    }

    /**
     * Retrieve redirect path
     *
     * @return string
     */
    public function redirectPath()
    {
        return route('crm.assignments.index');
    }
}
