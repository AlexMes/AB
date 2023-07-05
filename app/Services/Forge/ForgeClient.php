<?php

namespace App\Services\Forge;

use App\Server;
use App\Site;
use Illuminate\Support\Facades\Http;

class ForgeClient
{
    /**
     * Forge API token
     *
     * @var mixed
     */
    private $token;

    /**
     * ForgeClient constructor.
     *
     * @param mixed $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Make GET request to API and return JSON
     *
     * @param string $path
     * @param array  $query
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array|mixed
     */
    protected function get(string $path, $query = [])
    {
        return Http::withToken($this->token)
            ->get(sprintf('https://forge.laravel.com/api/v1/%s', $path), $query)
            ->throw()
            ->json();
    }

    /**
     * Get all visible servers
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array|mixed
     *
     */
    public function servers()
    {
        return collect($this->get('servers')['servers']);
    }

    /**
     * Get all server sites
     *
     * @param Server $server
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    public function sites(Server $server)
    {
        return collect($this->get(sprintf("servers/%s/sites", $server->forge_id))['sites']);
    }

    /**
     * Get all site certificates
     *
     * @param Site $site
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    public function certificates(Site $site)
    {
        return collect($this->get(sprintf(
            "servers/%s/sites/%s/certificates",
            $site->server->forge_id,
            $site->forge_id
        ))['certificates']);
    }
}
