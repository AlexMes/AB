<?php

namespace App\Http\Controllers;

use App\Deposit;
use App\Http\Requests\Deposits\Create;
use App\Http\Requests\Deposits\Update;
use App\Lead;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DepositsController extends Controller
{

    /**
     * DepositsController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Deposit::class, 'deposit');
    }

    /**
     * List visible deposits
     *
     * @param Request $request
     *
     * @return Deposit[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index(Request $request)
    {
        if (!empty($request->search) && auth()->user()->isSupport() || auth()->user()->isSubSupport()) {
            Log::channel('search-deposits')->info(sprintf(
                '%s(%d) attempted to search with \'%s\'',
                auth()->user()->name,
                auth()->id(),
                $request->search
            ));
        }

        return Deposit::visible()
            ->allowedOffers()
            ->when(auth()->user()->isSubSupport() && strlen($request->search) < 5, fn ($query) => $query->where('id', '<', 0))
            ->when($request->input('search'), function (Builder $builder, $input) {
                return $builder->searchIn(['phone'], $input)
                    ->orWhereHas('lead', fn ($query) => $query->searchIn(['email'], $input));
            })
            ->when($request->filled('user_id'), function (Builder $builder) use ($request) {
                if ($request->get('user_id') === 'null') {
                    return $builder->whereNull('user_id');
                }

                return $builder->where('user_id', $request->get('user_id'));
            })
            ->when($request->filled('office_id'), function (Builder $builder) use ($request) {
                if ($request->get('office_id') === 'null') {
                    return $builder->whereNull('office_id');
                }

                return $builder->where('office_id', $request->get('office_id'));
            })
            ->when($request->filled('offer_id'), function (Builder $builder) use ($request) {
                if ($request->get('offer_id') === 'null') {
                    return $builder->whereNull('deposits.offer_id');
                }

                return $builder->where('deposits.offer_id', $request->get('offer_id'));
            })
            ->when($request->has('lead_return_date'), function (Builder $builder) use ($request) {
                $period = json_decode($request->get('lead_return_date'), true);

                return $builder->whereBetween(
                    'lead_return_date',
                    [
                        $period['since'],
                        $period['until']
                    ]
                );
            })
            ->when($request->has('date'), function (Builder $builder) use ($request) {
                $period = json_decode($request->get('date'), true);

                return $builder->whereBetween(
                    'deposits.date',
                    [
                        $period['since'],
                        $period['until']
                    ]
                );
            })
            ->when($request->filled('utm_campaign'), function (Builder $builder) use ($request) {
                if ($request->get('utm_campaign') === 'null') {
                    return $builder->whereHas('lead', fn ($query) => $query->whereNull('utm_campaign'));
                }

                return $builder->whereIn(
                    'deposits.lead_id',
                    Lead::where('utm_campaign', $request->get('utm_campaign'))->pluck('id')
                );
            })
            ->when($request->filled('utm_campaign'), function (Builder $builder) use ($request) {
                if ($request->get('utm_campaign') === 'null') {
                    return $builder->whereHas('lead', fn ($query) => $query->whereNull('utm_campaign'));
                }

                return $builder->whereIn(
                    'lead_id',
                    Lead::where('utm_campaign', $request->get('utm_campaign'))->pluck('id')
                );
            })
            ->when($request->filled('branch_id'), function (Builder $query) use ($request) {
                return $query->whereIn('user_id', User::visible()->where('branch_id', $request->input('branch_id'))->pluck('id'));
            })
            ->when($request->filled('team_id'), function (Builder $query) use ($request) {
                return $query->whereIn('user_id', User::visible()->whereHas('teams', function ($teamQuery) use ($request) {
                    return $teamQuery->where('teams.id', $request->input('team_id'));
                })->pluck('id'));
            })
            ->when($request->filled('app'), function (Builder $query) use ($request) {
                return $query->whereIn(
                    'lead_id',
                    Lead::where('app_id', $request->get('app'))->pluck('id')
                );
            })
            ->when($request->filled('office_group_id'), function (Builder $query) use ($request) {
                return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) use ($request) {
                    return $q->selectRaw('1')
                        ->from('office_office_group')
                        ->whereColumn('deposits.office_id', 'office_office_group.office_id')
                        ->whereIn('office_office_group.group_id', Arr::wrap($request->input('office_group_id')));
                });
            })
            ->when(Auth::user()->isBuyer(), function (Builder $builder) use ($request) {
                return $builder->select(
                    'deposits.id',
                    'deposits.lead_return_date',
                    'deposits.date',
                    'deposits.sum',
                    'deposits.account_id',
                    'deposits.offer_id',
                    'deposits.office_id',
                    'deposits.lead_id',
                    'deposits.updated_at'
                );
            })
            ->with('user', 'account', 'office', 'offer', 'lead')
            ->orderByDesc('deposits.id')
            ->when(
                $request->boolean('all'),
                fn (Builder $builder) => $builder->get(),
                fn (Builder $builder) => $builder->paginate()
            );
    }

    /**
     * @param Deposit $deposit
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(Deposit $deposit)
    {
        return response($deposit->load('user', 'account', 'office', 'offer', 'lead'));
    }

    /**
     * @param Create $request
     *
     * @return \App\Deposit|\Illuminate\Database\Eloquent\Model
     */
    public function store(Create $request)
    {
        return Deposit::create($request->validated());
    }

    /**
     * @param Deposit $deposit
     * @param Update  $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(Deposit $deposit, Update $request)
    {
        return response(
            tap($deposit)->update($request->validated())
                ->loadMissing(['user', 'office', 'offer', 'account', 'lead']),
            202
        );
    }

    /**
     * @param Deposit $deposit
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(Deposit $deposit, Request $request)
    {
        $this->authorize('delete', $deposit);

        if ($request->boolean('recreate_deposit', null) === false) {
            $deposit->lead->update([
                'recreate_deposit' => false
            ]);
        }

        $deposit->delete();

        return response("Deleted", 204);
    }
}
