<?php

namespace App\Http\Controllers\Users;

use App\Events\OfferAllowed;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateAllowedOffer;
use App\Http\Requests\Users\DeleteAllowedOffer;
use App\Offer;
use App\Trail;
use App\User;

class AllowedOffersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index(User $user)
    {
        $this->authorize('viewAny', Offer::class);

        return response()->json($user->allowedOffers()->orderBy('id')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Users\CreateAllowedOffer $request
     * @param User                                        $user
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(CreateAllowedOffer $request, User $user)
    {
        $offer = Offer::findOrFail($request->input('offer_id'));

        if ($user->allowedOffers()->where('offer_id', $offer->id)->doesntExist()) {
            app(Trail::class)->add('User '.auth()->user()->name.' attached access to '. $offer->name);

            $user->allowedOffers()->attach($offer);

            OfferAllowed::dispatch($offer, $user);
        }

        return response()->json($offer, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteAllowedOffer $request
     * @param \App\User          $user
     * @param Offer              $allowedOffer
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(DeleteAllowedOffer $request, User $user, Offer $allowedOffer)
    {
        $user->allowedOffers()->detach($allowedOffer);

        app(Trail::class)->add('User '.auth()->user()->name.' detached access to '. $allowedOffer->name);

        return response()->json($allowedOffer, 202);
    }
}
