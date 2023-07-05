<?php

namespace App\Binom;

use App\Binom\Exceptions\BinomReponseException;
use Illuminate\Support\Facades\Http;
use Zttp\Zttp;

class Binom
{
    /**
     * API key for the Binom
     *
     * @var string
     */
    private $token;

    /**
     * Binom URl
     *
     * @var string
     */
    protected $url;

    /**
     * Binom constructor.
     *
     * @param string $token
     * @param string $url
     *
     * @return void
     */
    public function __construct($url, $token)
    {
        $this->token = $token;
        $this->url   = $url;
    }

    /**
     * Collect offers from Binom
     *
     * @return mixed
     */
    public function getOffers()
    {
        return Zttp::get($this->url, [
            'action'  => 'offer_getall',
            'api_key' => $this->token,
        ])->json();
    }

    /**
     * Collect landings from Binom
     *
     * @return array
     */
    public function getLandings()
    {
        return Zttp::get($this->url, [
            'action'  => 'landing_getall',
            'api_key' => $this->token,
        ])->json();
    }

    /**
     * Get campaigns list from binom
     *
     * @param array $filters
     *
     * @return mixed
     */
    public function getCampaigns($filters = [])
    {
        return Zttp::get($this->url, array_merge([
            'page'    => 'Campaigns',
            'api_key' => $this->token,
        ], $filters))->json();
    }

    /**
     * Get conversions from the binom
     *
     * @param array $filters
     *
     * @return mixed
     */
    public function getConversions($filters = [])
    {
        return Zttp::get($this->url, array_merge([
            'page'     => 'Conversions',
            'date_e'   => '2019-12-31',
            'date_s'   => '2019-12-01',
            'date'     => 12,
            'val_page' => 'All',
            'camp_id'  => 43,
            'api_key'  => $this->token,
        ], $filters))->json();
    }

    /**
     * Get detailed statistics from Binom campaign
     *
     * @param \App\Binom\Campaign $campaign
     * @param array               $filters
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getStatistics(Campaign $campaign, $filters = [])
    {
        $response = Zttp::get($this->url, array_merge([
            'page'     => 'Stats',
            'camp_id'  => $campaign->id,
            'group1'   => 287,
            'val_page' => 'All',
            'date'     => 12,
            'api_key'  => $this->token,
        ], $filters))->json();

        // Check for errors from Binom response,
        // cause that shit responds with 2xx codes on errors. Fucking idiots
        if (is_array($response) && array_key_exists('status', $response)) {
            throw new BinomReponseException(
                $response['status']['error'] ?? $response['status'] ?? 'Something totally wrong.'
            );
        }

        // Double check that we have actual stats
        if (is_array($response)) {
            return $response;
        }

        return [];
    }

    public function getTrafficSources()
    {
        return Zttp::get($this->url, [
            'page'    => 'Traffic_Sources',
            'api_key' => $this->token,
        ])->json();
    }

    /**
     * Load click info from the Binom
     *
     * @param string $clickId
     *
     * @throws \App\Binom\Exceptions\BinomReponseException
     *
     * @return array|null
     */
    public function getClick(string $clickId)
    {
        $response = Http::timeout(5)->get(sprintf('%s/arm.php', $this->url), [
            'api_key' => $this->token,
            'action'  => 'clickinfo@get',
            'clickid' => $clickId
        ])->json();

        if (! array_key_exists('click', $response)) {
            throw new BinomReponseException('Click info missing from response.');
        }

        return $response['click'];
    }
}
