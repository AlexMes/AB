<?php

namespace App\Http\Controllers\Adsets;

use App\AdsBoard;
use App\Facebook\Account;
use App\Facebook\AdSet;
use App\Facebook\Jobs\StopAdset;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MassStop extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('massStop', AdSet::class);

        $query = AdSet::active()
            ->when(auth()->user()->isAdmin() && ! empty($request->users), function ($query) use ($request) {
                $query->whereIn('account_id', Account::forUsers($request->users)->map->account_id);
            })
            ->when(auth()->user()->isBuyer(), function ($query) {
                $query->whereIn('account_id', auth()->user()->accounts()->pluck('account_id')->toArray());
            })
            ->when(auth()->user()->isVerifier(), function ($query) {
                $query->whereIn('account_id', auth()->user()->accounts()->pluck('account_id')->toArray());
            })
            ->when($request->offers, function (Builder $query) use ($request) {
                $query->whereHas('campaign', fn (Builder $q) => $q->whereIn('offer_id', $request->offers));
            });

        $query->chunk(50, static function ($adsets) {
            $adsets->each(function ($adset) {
                StopAdset::dispatch($adset)->allOnQueue(AdsBoard::QUEUE_FACEBOOK);
            });
        });

        if ($count = $query->count()) {
            return response(sprintf('Stopping %s adsets have been started', $count), 200);
        }

        return response(sprintf("There are no adsets available for stopping"), 202);
    }
}
