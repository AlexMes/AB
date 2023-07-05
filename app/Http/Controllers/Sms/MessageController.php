<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\SmsMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return void
     */
    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', SmsMessage::class);

        return SmsMessage::query()
            ->whereHas(
                'campaign',
                fn ($query) => $query->where('branch_id', $request->input('branch', auth()->user()->branch_id))
            )
            ->searchIn(['id', 'phone'], $request->get('search'))
            ->when($request->get('since'), function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->get('since'))->startOfDay()->toDateTimeString(),
                    Carbon::parse($request->get('until'))->endOfDay()->toDateTimeString(),
                ]);
            })
            ->notEmptyWhereIn('campaign_id', $request->get('campaigns'))
            ->notEmptyWhere('campaign_id', $request->get('campaign_id'))
            ->with('lead')
            ->orderByDesc('id')
            ->paginate();
    }
}
