<?php

namespace App\Http\Controllers\Campaigns;

use App\AdsBoard;
use App\Facebook\Account;
use App\Facebook\Campaign;
use App\Facebook\Jobs\StopCampaign;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MassStop extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('massStop', Campaign::class);

        $query = Campaign::active()
            ->when(auth()->user()->isAdmin() && ! empty($request->users), function ($query) use ($request) {
                $query->whereIn('account_id', Account::forUsers($request->users)->map->account_id);
            })
            ->when(auth()->user()->isBuyer(), function ($query) {
                $query->whereIn('account_id', auth()->user()->accounts()->pluck('account_id')->toArray());
            })
            ->when(auth()->user()->isVerifier(), function ($query) {
                $query->whereIn('account_id', auth()->user()->accounts()->pluck('account_id')->toArray());
            })
            ->notEmptyWhereIn('offer_id', $request->offers);

        $query->chunk(50, static function ($campaigns) {
            $campaigns->each(function ($campaign) {
                StopCampaign::dispatch($campaign)->allOnQueue(AdsBoard::QUEUE_FACEBOOK);
            });
        });

        if ($count = $query->count()) {
            return response(sprintf('Stopping %s campaigns have been started', $count), 200);
        }

        return response("There are no campaigns available for stopping", 204);
    }
}
