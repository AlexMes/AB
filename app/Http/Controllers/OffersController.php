<?php

namespace App\Http\Controllers;

use App\Http\Requests\Offers\CreateOfferRequest;
use App\Http\Requests\Offers\UpdateOfferRequest;
use App\Offer;
use Illuminate\Http\Request;

class OffersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return Offer::allowed()
            ->searchIn(['name','uuid'], $request->input('search'))
            ->orderByDesc('id')
            ->when(
                $request->filled('paginate'),
                fn ($query) => $query->paginate(),
                fn ($query) => $query->get()
            );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateOfferRequest $request
     *
     * @return Offer
     */
    public function store(CreateOfferRequest $request)
    {
        return Offer::create($request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param Offer $offer
     *
     * @return Offer
     */
    public function show(Offer $offer)
    {
        return $offer;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateOfferRequest $request
     * @param Offer              $offer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        return response()->json(tap($offer)->update($request->validated()), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Offer $offer
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(Offer $offer)
    {
        return response('Forbidden', 403);
    }
}
