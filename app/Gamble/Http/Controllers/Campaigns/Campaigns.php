<?php

namespace App\Gamble\Http\Controllers\Campaigns;

use App\Gamble\Campaign;
use App\Gamble\Http\Requests\Campaigns\Create;
use App\Gamble\Http\Requests\Campaigns\Update;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Campaigns extends Controller
{
    /**
     * Campaigns constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Campaign::class);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return Campaign::visible()
            ->with(['account', 'offer'])
            ->orderByDesc('created_at')
            ->when(
                $request->has('all'),
                fn ($query) => $query->get(),
                fn ($query) => $query->paginate()
            );
    }

    /**
     * @param Campaign $campaign
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Campaign $campaign)
    {
        return response()->json($campaign->load(['account', 'offer']));
    }

    /**
     * @param Create $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Create $request)
    {
        return response()->json(Campaign::create($request->validated())->load(['account', 'offer']), 201);
    }

    /**
     * @param Campaign $campaign
     * @param Update   $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Campaign $campaign, Update $request)
    {
        $campaign->update($request->validated());

        return response()->json($campaign->fresh(['account', 'offer']), 202);
    }
}
