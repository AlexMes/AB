<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class GoogleTFASecret extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isDeveloper()) {
            abort(403);
        }

        return response()->json([
            'google_tfa_secret' => tap($user)->generateGoogleTfaSecret()->google_tfa_secret,
            'qr_code'           => $user->getGoogleTFAQRCode(),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isDeveloper()) {
            abort(403);
        }

        return response()->json([
            'google_tfa_secret' => $user->google_tfa_secret,
            'qr_code'           => $user->getGoogleTFAQRCode(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isDeveloper()) {
            abort(403);
        }

        $user->update(['google_tfa_secret' => null]);

        return response()->noContent();
    }
}
