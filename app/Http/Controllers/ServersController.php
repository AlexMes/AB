<?php

namespace App\Http\Controllers;

use App\Server;
use Illuminate\Http\Request;

class ServersController extends Controller
{
    /**
     * DomainsController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Server::class, 'server');
    }

    /**
     * @param Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return Server[]|\Illuminate\Database\Eloquent\Collection
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Server::class);

        return Server::searchIn(['name', 'provider', 'ip_address'], $request->get('search'))
            ->withCount('domains')
            ->paginate($request->get('perPage') ?? 15);
    }

    /**
     * @param Server $server
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show(Server $server)
    {
        $this->authorize('view', $server);

        return response($server->loadCount('domains'));
    }

    /**
     * @param Server $server
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy(Server $server)
    {
        $this->authorize('destroy', $server);

        $server->delete();

        return response("Deleted", 204);
    }
}
