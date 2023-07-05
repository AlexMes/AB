<?php

namespace App\Deluge\Http\Controllers;

use App\Deluge\Http\Requests\AccountsPours as Request;
use App\Http\Controllers\Controller;
use App\ManualAccount;
use App\ManualPour;

class AccountsPours extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Deluge\Http\Requests\AccountsPours $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $pour = ManualPour::firstOrCreate(
            [
                'date'    => $request->input('date'),
                'user_id' => auth()->id(),
            ]
        );

        $accounts = ManualAccount::query()
            ->visible()
            ->whereIn(
                'account_id',
                collect($request->input('accounts'))
                    ->diff($pour->accounts()->pluck('manual_accounts.account_id'))
            )
            ->get()
            ->keyBy('account_id');

        $accounts->each->update(['moderation_status' => ManualAccount::MODERATION_REVIEW]);

        $pour->accounts()->syncWithoutDetaching(
            collect($accounts->pluck('account_id'))
                ->mapWithKeys(fn ($accId) => [
                    $accId => [
                        'status'            => optional($accounts->get($accId))->status,
                        'moderation_status' => ManualAccount::MODERATION_REVIEW,
                    ],
                ])
        );

        return redirect()->route('deluge.pours.show', $pour);
    }
}
