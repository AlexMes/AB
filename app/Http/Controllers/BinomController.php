<?php

namespace App\Http\Controllers;

use App\Affiliate;
use App\Binom;
use App\Http\Requests\Binoms\Create;
use App\Http\Requests\Binoms\Update;
use Illuminate\Http\Request;

class BinomController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Binom::class, 'binom');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Affiliate[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Binom::query()
            ->searchIn('name', $request->search)
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Binoms\Create $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        $binom = Binom::create($request->validated());

        return response()->json($binom, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Binom $binom
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(Binom $binom)
    {
        return response()->json(
            $binom->makeVisible('access_token')
                ->load('trafficSources.innerTrafficSource'),
            200
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Binoms\Update $request
     * @param \App\Binom                       $binom
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, Binom $binom)
    {
        $binom->update($request->validated());

        return response()->json($binom, 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Binom $binom
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Binom $binom)
    {
        $binom->delete();

        return response(204);
    }
}
