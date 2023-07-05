<?php

namespace App\Traits;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FALaravel\Support\Authenticator;

trait GoogleTwoFactorAuth
{
    use AuthenticatesUsers {
        AuthenticatesUsers::login as baseLogin;
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->guard()->validate($this->credentials($request))) {
            $this->guard()->setUser($this->guard()->getLastAttempted());

            if ($this->isUsed() && !app(Authenticator::class)->boot($request)->isAuthenticated()) {
                $request->session()->put($this->sessionKey(), $this->guard()->user()->getKey());

                return redirect()->route($this->routeName());
            }
        }

        return $this->baseLogin($request);
    }

    /**
     * @return string
     */
    protected function sessionKey(): string
    {
        return config('google2fa.session_var') . '.model_key';
    }

    /**
     * Route name for google two factor login page
     *
     * @return string
     */
    protected function routeName(): string
    {
        return 'google-tfa.login';
    }

    /**
     *  View name for google two factor login page
     *
     * @return string
     */
    protected function viewName(): string
    {
        return config('google2fa.view', 'auth.google-tfa');
    }

    /**
     * Determines additional conditions to use two factor auth
     *
     * @return bool
     */
    protected function isUsed(): bool
    {
        return true;
    }

    /**
     * Show the form for google two factor auth.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function showTFAForm(Request $request)
    {
        if ($this->guard()->check()) {
            $request->session()->put($this->sessionKey(), $this->guard()->user()->getKey());
        }

        if (!$request->session()->has($this->sessionKey())) {
            return redirect()->intended($this->redirectPath());
        }

        return view($this->viewName());
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws ValidationException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function loginTFA(Request $request)
    {
        if (
            $this->guard()->onceUsingId($request->session()->get($this->sessionKey()))
            && app(Authenticator::class)->boot($request)->isAuthenticated()
        ) {
            $this->guard()->login($this->guard()->user());

            $request->session()->regenerate();

            return redirect()->intended($this->redirectPath());
        }

        throw ValidationException::withMessages([
            'message' => config('google2fa.error_messages.wrong_otp'),
        ]);
    }
}
