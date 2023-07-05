<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Http\Requests\Domains\Create;
use App\Http\Requests\Domains\Update;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DomainsController extends Controller
{
    /**
     * DomainsController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Domain::class, 'domain');
    }

    /**
     * @param Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return Domain[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Domain::class);

        return Domain::visible()
            ->searchIn(['url'], $request->get('search'))
            ->when($request->filled('effectiveDate'), function (Builder $builder) use ($request) {
                if ($request->get('effectiveDate') === 'all') {
                    return $builder;
                }

                if ($request->get('effectiveDate') === '') {
                    return $builder;
                }

                return $builder->whereDate('effectiveDate', $request->get('effectiveDate'));
            })
            ->when($request->filled('linkType'), function (Builder $builder) use ($request) {
                if ($request->get('linkType') === 'all') {
                    return $builder;
                }

                return $builder->where('linkType', $request->get('linkType'));
            })
            ->when($request->filled('offer_id'), function (Builder $builder) use ($request) {
                if ($request->get('offer_id') === 'all') {
                    return $builder;
                }

                return $builder->where('offer_id', $request->get('offer_id'));
            })
            ->when($request->filled('status'), function (Builder $builder) use ($request) {
                if ($request->get('status') === 'all') {
                    return $builder;
                }

                return $builder->where('status', $request->get('status'));
            })
            ->when($request->filled('user_id'), function (Builder $builder) use ($request) {
                if ($request->get('user_id') === 'all') {
                    return $builder;
                }

                return $builder->where('user_id', $request->get('user_id'));
            }, function (Builder $builder) use ($request) {
                return $builder->where('user_id', $request->get('user_id'));
            })
            ->when($request->filled('landing_id'), function (Builder $builder) use ($request) {
                if ($request->get('landing_id') === 'all') {
                    return $builder;
                }

                return $builder->where('landing_id', $request->get('landing_id'));
            })
            ->when($request->filled('sp_id'), function (Builder $builder) use ($request) {
                if ($request->get('sp_id') === 'all') {
                    return $builder;
                }

                return $builder->where('sp_id', $request->get('sp_id'));
            }, function (Builder $builder) use ($request) {
                return $builder->where('sp_id', $request->get('sp_id'));
            })
            ->when($request->filled('bp_id'), function (Builder $builder) use ($request) {
                if ($request->get('bp_id') === 'all') {
                    return $builder;
                }

                return $builder->where('bp_id', $request->get('bp_id'));
            }, function (Builder $builder) use ($request) {
                return $builder->where('bp_id', $request->get('bp_id'));
            })
            ->when($request->filled('type'), function (Builder $builder) use ($request) {
                return $builder->where('linkType', $request->get('type'));
            })
            ->with(['user', 'order', 'offer', 'sp', 'bp'])
            ->orderByDesc('effectiveDate')
            ->paginate($request->get('perPage') ?? 15);
    }

    /**
     * @param Domain $domain
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(Domain $domain)
    {
        $this->authorize('view', $domain);

        return response($domain->load('user', 'order', 'offer'));
    }

    /**
     * @param Create $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        $domain = Domain::create($request->validated());

        return response($domain, 201);
    }

    /**
     * @param Domain $domain
     * @param Update $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function update(Domain $domain, Update $request)
    {
        $domain->update($request->validated());

        return response($domain->load('user'), 202);
    }

    /**
     * @param Domain $domain
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(Domain $domain)
    {
        $this->authorize('destroy', $domain);

        $domain->delete();

        return response("Deleted", 204);
    }
}
