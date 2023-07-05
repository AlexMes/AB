<?php

namespace App\Deluge\Http\Controllers;

use App\Deluge\Domain;
use App\Deluge\Http\Requests\CreateDomain;
use App\Deluge\Http\Requests\UpdateDomain;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class DomainsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Domain::class);
    }
    /**
     * Index visible domains
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function index(Request $request)
    {
        return view('deluge::domains.index')->with([
            'domains' => Domain::visible()
                ->when($request->filled('user'), fn ($query) => $query->where('user_id', $request->input('user')))
                ->orderBy('down_at')
                ->paginate(),
            'users' => User::visible()->get(['id','name']),
        ]);
    }

    /**
     * Show single domain details
     *
     * @return void
     */
    public function show(Domain $domain)
    {
        return view('deluge::domains.show')->with(['domain' => $domain]);
    }


    /**
     * Create new domain(s)
     *
     * @return void
     */
    public function create()
    {
        return view('deluge::domains.form')->with([
            'users' => User::visible()->get(['id','name']),
        ]);
    }


    public function store(CreateDomain $request)
    {
        $domains = [];
        $created = collect();

        preg_match_all('/(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]/', $request->input('url'), $domains);


        foreach ($domains[0] as $domain) {
            $created->add(Domain::updateOrCreate(['url' => $domain], [
                'user_id'     => $request->input('user_id'),
                'destination' => $request->input('destination')
            ]));
        }

        $created->each->check();

        return redirect()->route('deluge.domains.index')
            ->with('success', 'Domains '.$created->pluck('url')->implode(','). ' created with destination '.$request->input('destination'));
    }


    public function edit(Domain $domain)
    {
        return view('deluge::domains.form')->with([
            'domain' => $domain,
            'users'  => User::visible()->get(['id','name']),
        ]);
    }


    public function update(Domain $domain, UpdateDomain $request)
    {
        $domain->update($request->validated());

        $domain->check();

        return redirect()->route('deluge.domains.show', $domain)->with('success', 'Domain was updated');
    }
}
