<?php

namespace App\CRM\Http\Controllers;

use App\AdsBoard;
use App\CRM\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthCallback extends Controller
{

    /**
     * Handles response from FRX crm
     *
     * @param \App\CRM\Tenant          $tenant
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function __invoke(Tenant $tenant, Request $request)
    {
        $state = $request->session()->pull('state');

        if (strlen($state) < 1 || $state !== $request->state) {
            return redirect()->route('login', $tenant)->withErrors([
                'access' => 'Request probably malformed, please try again'
            ]);
        }

        if ($request->has('error') && $request->get('error') === 'access_denied') {
            return redirect()->route('login', $tenant)->withErrors([
                'access' => 'You have been denied acccess to your account, please try again',
            ]);
        }

        try {
            $token   = $tenant->convertAuthorizationCodeToAccessToken($request->code);
            $manager = $tenant->getUser($token);
        } catch (\Throwable $th) {
            return redirect()->route('login', $tenant)->withErrors([
                'access' => 'Your CRM application unreachable for us, please contact support',
            ]);
        }

        /** @var App\Manager */
        $internalManager = $tenant->managers()
            ->withTrashed()
            ->where(fn ($query) => $query->where('frx_user_id', $manager['id'])->orWhere('email', $manager['email']))
            ->orderBy('frx_user_id');

        if ($internalManager->value('id') === null) {
            $user = $tenant->managers()->create([
                'frx_user_id'      => $manager['id'],
                'frx_role'         => $manager['role_key'],
                'frx_access_token' => $token,
                'office_id'        => optional($tenant->offices()->where('frx_office_id', $manager['office_id'])->first())->id,
                'name'             => $manager['fullname'],
                'email'            => $manager['email'],
                'locale'           => $manager['locale'] ?? 'ru',
            ]);
        } else {
            $user = $internalManager->first();
            if ($user->trashed()) {
                AdsBoard::report('Trashed attempt to login with email '. $manager['email']. ' as user ' . $user->email);

                return redirect()->route('login', $tenant)->withErrors([
                    'access' => "Your account is blocked, please contact support, and provide email you've used to sign in",
                ]);
            }
            $user->update([
                'name'             => $manager['fullname'],
                'frx_role'         => $manager['role_key'],
                'frx_access_token' => $token,
                'office_id'        => optional($tenant->offices->where('frx_office_id', $manager['office_id'])->first())->id,
                'locale'           => $manager['locale'] ?? 'ru',
            ]);
        }

        config([
            'session.domain' => config('crm.domain')
        ]);

        Auth::guard('crm')->login($user);

        return redirect()->route('crm.assignments.index');
    }
}
