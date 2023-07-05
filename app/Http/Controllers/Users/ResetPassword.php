<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\ResetUserPassword;
use App\User;

class ResetPassword extends Controller
{

    /**
     * @param User              $user
     * @param ResetUserPassword $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(User $user, ResetUserPassword $request)
    {
        return response()->json(
            tap($user)->update(['password' => $request->validated()['password']]),
            202
        );
    }
}
