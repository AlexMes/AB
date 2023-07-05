<?php

namespace App\Gamble\Http\Controllers;

use App\Gamble\Http\Requests\Offers\Create;
use App\Gamble\Http\Requests\Offers\Update;
use App\Gamble\Offer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Offers extends Controller
{
    /**
     * Offers constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Offer::class);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return Offer::query()
            ->orderBy('name')
            ->when(
                $request->has('all'),
                fn ($query) => $query->get(),
                fn ($query) => $query->paginate()
            );
    }

    /**
     * @param Offer $offer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Offer $offer)
    {
        return response()->json($offer);
    }

    /**
     * @param Create $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Create $request)
    {
        return response()->json(Offer::create($request->validated()), 201);
    }

    /**
     * @param Offer  $offer
     * @param Update $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Offer $offer, Update $request)
    {
        $offer->update($request->validated());

        return response()->json($offer->fresh(), 202);
    }
}
