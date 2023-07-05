<?php

namespace App\Http\Controllers;

use App\Http\Requests\DistributeLeftovers;
use App\Lead;
use App\LeadAssigner\LeadAssigner;
use App\Offer;
use App\Trail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class DistributeLeftoversController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param DistributeLeftovers $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(DistributeLeftovers $request)
    {
        $lock = Cache::lock('leftovers-distribution', 15);

        if ($lock->get()) {
            foreach (
                Lead::visible()->leftovers($request->input('date'))
                    ->whereIn(
                        'offer_id',
                        Offer::leftovers()
                            ->whereHas('routes', fn ($query) => $query->current()->visible()->active()->incomplete())
                            ->notEmptyWhereIn('id', Arr::wrap($request->input('offer_id')))
                            ->pluck('id')
                    )
                    ->latest()
                    ->cursor() as $lead
            ) {
                app(Trail::class)->add('Distributing LO leftover lead '.$lead->id);
                LeadAssigner::dispatchNow($lead);
                sleep(1);
            }

            return response()->noContent();
        }

        return response()->json(['message' => 'Distribution already running'], 400);
    }
}
