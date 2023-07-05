<?php

namespace App\Http\Controllers;

use App\AdsBoard;
use App\Http\Requests\SmoothAssignLeftOversRequest;
use App\Jobs\RunLeftOversAssignment;
use Cache;

class SmoothAssignOrdersLeftOvers extends Controller
{
    /**
     * Push assigner jobs for leads
     *
     * @param SmoothAssignLeftOversRequest $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(SmoothAssignLeftOversRequest $request)
    {
        $lock = Cache::lock('leftovers-smooth-split', 15);

        if ($lock->get()) {
            RunLeftOversAssignment::dispatchNow(
                $request->input('date'),
                $request->input('amount'),
                $request->input('offers'),
                $request->input('offices'),
                $request->input('leads'),
                $request->boolean('simulate_autologin'),
                $request->input('deliver_until'),
                false,
                $request->boolean('replace') ? auth()->id() : null,
                $request->input('deliver_since'),
                $request->input('sort_order', 'asc'),
                $request->boolean('replace') ? $request->input('destination') : null
            );

            $this->report($request->user()->name, $request->all());

            return response()->noContent();
        }

        $this->report($request->user()->name, $request->all());

        return response()->json(['message' => 'Splitter already running'], 400);
    }


    /**
     * @param string $user
     * @param array  $params
     *
     * @return void
     */
    public function report(string $user, array $params)
    {
        AdsBoard::report(
            sprintf(
                "Leftovers smooth split started by %s. Params: %s",
                $user,
                json_encode($params)
            )
        );
    }
}
