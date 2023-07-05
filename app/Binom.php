<?php

namespace App;

use App\Binom\Campaign;
use App\Binom\Exceptions\BinomReponseException;
use App\Binom\Statistic;
use App\Binom\TrafficSource;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

/**
 * App\Binom
 *
 * @property int $id
 * @property string $name
 * @property bool $enabled
 * @property string $url
 * @property string $access_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Campaign[] $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Statistic[] $statistics
 * @property-read int|null $statistics_count
 * @property-read \Illuminate\Database\Eloquent\Collection|TrafficSource[] $trafficSources
 * @property-read int|null $traffic_sources_count
 *
 * @method static Builder|Binom active()
 * @method static Builder|Binom newModelQuery()
 * @method static Builder|Binom newQuery()
 * @method static Builder|Binom query()
 * @method static Builder|Binom whereAccessToken($value)
 * @method static Builder|Binom whereCreatedAt($value)
 * @method static Builder|Binom whereEnabled($value)
 * @method static Builder|Binom whereId($value)
 * @method static Builder|Binom whereName($value)
 * @method static Builder|Binom whereUpdatedAt($value)
 * @method static Builder|Binom whereUrl($value)
 * @mixin \Eloquent
 */
class Binom extends Model
{
    /**
     * Guard model attribute
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Hide model attributes from JSON response
     *
     * @var string[]
     */
    protected $hidden = ['access_token'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trafficSources()
    {
        return $this->hasMany(TrafficSource::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statistics()
    {
        return $this->hasMany(Statistic::class);
    }

    /**
     * Limit to enabled Binom instances
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $builder)
    {
        return $builder->where('enabled', true);
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
        $response = Http::get(sprintf('%s/arm.php', $this->url), [
            'api_key' => $this->access_token,
            'action'  => 'clickinfo@get',
            'clickid' => $clickId
        ])->json();

        if (! array_key_exists('click', $response)) {
            BinomReponseException::clickIsMissing();
        }

        return $response['click'];
    }

    /**
     * Load traffic sources from Binom
     *
     * @return mixed
     */
    public function getTrafficSources()
    {
        return Http::get($this->url, [
            'page'    => 'Traffic_Sources',
            'api_key' => $this->access_token,
        ])->json();
    }

    /**
     * Get campaign list from the Binom
     *
     * @param array $filters
     *
     * @return mixed
     */
    public function getCampaigns($filters = [])
    {
        return Http::get($this->url, array_merge([
            'page'    => 'Campaigns',
            'api_key' => $this->access_token,
        ], $filters))->json();
    }

    /**
     * Determine is Binom enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled === true;
    }

    /**
     * Enable Binom
     *
     * @return \App\Binom
     */
    public function enable(): Binom
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return tap($this)->update(['enabled' => true]);
    }

    /**
     * Disable Binom
     *
     * @return \App\Binom
     */
    public function disable(): Binom
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return tap($this)->update(['enabled' => false]);
    }

    /**
     * Collect statistics from the Binom API
     *
     * @param \App\Binom\Campaign $campaign
     * @param array               $filters
     *
     * @throws \App\Binom\Exceptions\BinomReponseException
     *
     * @return array
     */
    public function getStatistics(Campaign $campaign, $filters = [])
    {
        $response = Http::get($this->url, array_merge([
            'page'     => 'Stats',
            'camp_id'  => $campaign->campaign_id,
            'group1'   => 287,
            'val_page' => 'All',
            'date'     => 12,
            'timezone' => 3,
            'api_key'  => $this->access_token,
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

    /**
     * Get total number of leads, received on specific date
     *
     * @param \Carbon\CarbonInterface $date
     *
     * @return mixed
     */
    public function getTotalLeadsAmount(CarbonInterface $date)
    {
        $response = Http::get($this->url, [
            'page'     => 'Campaigns',
            'date_s'   => $date->toDateString(),
            'date_e'   => $date->toDateString(),
            'date'     => 12,
            'timezone' => 3,
            'api_key'  => $this->access_token,
        ])->json();

        return collect($response)->sum('leads');
    }

    /**
     * Send postback to Binom instance.
     *
     * @param $clickId
     * @param null $amount
     *
     * @return \Http\HttpResponse
     */
    public function sendPayout($clickId, $amount = null)
    {
        return Http::withoutVerifying()->get(sprintf("%s/click.php", $this->url), [
            'cnv_id'     => $clickId,
            'payout'     => $amount,
            'cnv_status' => 'sale'
        ])->json();
    }

    /**
     * @param $clickId
     *
     * @return array|mixed
     */
    public function sendLeadReceived($clickId)
    {
        return Http::withoutVerifying()->get(sprintf("%s/click.php", $this->url), [
            'cnv_id'     => $clickId,
            'payout'     => 'OPTIONAL',
            'cnv_status' => 'lead',
        ])->json();
    }
}
