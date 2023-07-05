<?php

namespace App\Http\Controllers\Forge;

use App\Http\Controllers\Controller;
use App\Server;

class CacheSites extends Controller
{
    /**
     * @param Server $server
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function __invoke(Server $server)
    {
        $server->pullSites();

        return response(['message' => "Кеширование сайтов с сервера {$server->name} запущено"]);
    }
}
