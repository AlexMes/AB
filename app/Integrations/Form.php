<?php

namespace App\Integrations;

use App\Domain;
use App\Integrations\Drivers\BitrixDriver;
use App\Integrations\Drivers\ConvertingTeamDriver;
use App\Integrations\Drivers\DefaultDriver;
use App\Integrations\Drivers\Fxg24Driver;
use App\Integrations\Drivers\IwixDriver;
use App\Integrations\Drivers\TrafficonDriver;
use App\Integrations\Jobs\LeadSender;
use App\Lead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Integrations\Form
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string|null $method
 * @property string|null $form_id
 * @property string|null $form_api_key
 * @property array|null $fields
 * @property bool $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $landing_id
 * @property string|null $provider
 * @property string|null $phone_prefix
 * @property string|null $external_offer_id
 * @property string|null $external_affiliate_id
 * @property-read Domain|null $landing
 * @property-read \Illuminate\Database\Eloquent\Collection|Lead[] $leads
 * @property-read int|null $leads_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Integrations\Payload[] $payloads
 * @property-read int|null $payloads_count
 *
 * @method static Builder|Form active()
 * @method static Builder|Form disabled()
 * @method static Builder|Form newModelQuery()
 * @method static Builder|Form newQuery()
 * @method static Builder|Form query()
 * @method static Builder|Form whereCreatedAt($value)
 * @method static Builder|Form whereExternalAffiliateId($value)
 * @method static Builder|Form whereExternalOfferId($value)
 * @method static Builder|Form whereFields($value)
 * @method static Builder|Form whereFormApiKey($value)
 * @method static Builder|Form whereFormId($value)
 * @method static Builder|Form whereId($value)
 * @method static Builder|Form whereLandingId($value)
 * @method static Builder|Form whereMethod($value)
 * @method static Builder|Form whereName($value)
 * @method static Builder|Form wherePhonePrefix($value)
 * @method static Builder|Form whereProvider($value)
 * @method static Builder|Form whereStatus($value)
 * @method static Builder|Form whereUpdatedAt($value)
 * @method static Builder|Form whereUrl($value)
 * @mixin \Eloquent
 */
class Form extends Model
{
    public const METHODS = [
        self::METHOD_GET,
        self::METHOD_POST,
    ];

    protected $providers = [
        self::PROVIDER_DEFAULT        => DefaultDriver::class,
        self::PROVIDER_CONVERTINGTEAM => ConvertingTeamDriver::class,
        self::IWIX                    => IwixDriver::class,
        self::TAFFICON                => TrafficonDriver::class,
        self::BITRIX                  => BitrixDriver::class,
        self::FXG24                   => Fxg24Driver::class
    ];

    /**
     * Default provider
     */
    public const PROVIDER_DEFAULT  = 'default';

    /**
     * Convertingteam provider
     */
    public const PROVIDER_CONVERTINGTEAM = 'convertingteam';

    /**
     * Iwix provider
     */
    public const IWIX = 'iwix';

    /**
     * Iwix provider
     */
    public const TAFFICON = 'trafficon';

    /**
     * Iwix provider
     */
    public const BITRIX = 'bitrix';

    /**
     * Iwix provider
     */
    public const FXG24 = 'fxg24';


    /**
     * GET method for postbacks
     */
    public const METHOD_GET  = 'get';

    /**
     * POST method for postbacks
     */
    public const METHOD_POST = 'post';

    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'itegrations_forms';

    /**
     * Guard attributes from mass-assignment
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $with = ['landing'];

    protected $casts = [
        'fields'           => 'array',
        'requestContents'  => 'array',
    ];

    /**
     * Payloads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payloads()
    {
        return $this->hasMany(Payload::class);
    }

    /**
     * Leads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Landing page
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function landing()
    {
        return  $this->belongsTo(Domain::class, 'landing_id');
    }

    /**
     * Scope for the active form
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for disabled form
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDisabled(Builder $query)
    {
        return $query->where('status', false);
    }

    /**
     *
     * @param \App\Lead $lead
     */
    public function dispatch(Lead $lead)
    {
        LeadSender::dispatch($lead, $this);
    }

    /**
     *
     * @param \App\Lead $lead
     *
     * @return mixed
     */
    public function getProvider()
    {
        return new $this->providers[$this->provider]($this);
    }
}
