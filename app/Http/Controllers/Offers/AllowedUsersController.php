<?php

namespace App\Http\Controllers\Offers;

use App\Events\OfferAllowed;
use App\Http\Controllers\Controller;
use App\Http\Requests\Offers\CreateAllowedUser;
use App\Http\Requests\Offers\DeleteAllowedUser;
use App\Offer;
use App\Trail;
use App\User;

class AllowedUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Offer $offer
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index(Offer $offer)
    {
        return response()->json($offer->allowedUsers()->orderBy('id')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateAllowedUser $request
     * @param Offer             $offer
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(CreateAllowedUser $request, Offer $offer)
    {
        $user = User::findOrFail($request->input('user_id'));

        if ($offer->allowedUsers()->where('user_id', $user->id)->doesntExist()) {
            app(Trail::class)->add('User '.auth()->user()->name.' attached access to '. $offer->name);

            $offer->allowedUsers()->attach($user);

            OfferAllowed::dispatch($offer, $user);
        }

        return response()->json($user, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteAllowedUser $request
     * @param \App\Offer        $offer
     * @param User              $allowedUser
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(DeleteAllowedUser $request, Offer $offer, User $allowedUser)
    {
        app(Trail::class)->add('User '.auth()->user()->name.' detached access to '. $offer->name);

        $offer->allowedUsers()->detach($allowedUser);

        return response()->json($allowedUser, 202);
    }
}
