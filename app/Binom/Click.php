<?php

namespace App\Binom;

use App\Binom;
use App\Events\ClickCreated;
use App\Lead;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Binom\Click
 *
 * @property int $id
 * @property string $clickid
 * @property int $lead_id
 * @property string|null $click_time
 * @property bool $lp_click
 * @property bool $unique
 * @property bool $conversion
 * @property string|null $conversion_time
 * @property int|null $campaign_id
 * @property string|null $campaign_name
 * @property int|null $path_id
 * @property string|null $offer_name
 * @property int|null $landing_id
 * @property string|null $landing_name
 * @property string|null $ip
 * @property string|null $country_name
 * @property string|null $country_code
 * @property string|null $city_name
 * @property string|null $isp_name
 * @property string|null $connection_type
 * @property string|null $user_agent
 * @property string|null $browser
 * @property string|null $browser_version
 * @property string|null $device_brand
 * @property string|null $device_model
 * @property string|null $device_name
 * @property string|null $display_size
 * @property string|null $display_resolution
 * @property string|null $device_pointing_method
 * @property string|null $os_name
 * @property string|null $os_version
 * @property string|null $referer_url
 * @property string|null $referer_domain
 * @property string|null $language
 * @property string|null $token_1_value
 * @property string|null $token_2_value
 * @property string|null $token_3_value
 * @property string|null $token_4_value
 * @property string|null $token_5_value
 * @property string|null $token_6_value
 * @property string|null $token_7_value
 * @property string|null $token_8_value
 * @property string|null $token_9_value
 * @property string|null $token_10_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $binom_id
 * @property-read Binom|null $binom
 * @property-read Lead $lead
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Click newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Click newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Click query()
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereBinomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereBrowserVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereCampaignName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereCityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereClickTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereClickid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereConnectionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereConversion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereConversionTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereCountryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereDeviceBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereDeviceModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereDeviceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereDevicePointingMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereDisplayResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereDisplaySize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereIspName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereLandingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereLandingName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereLpClick($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereOfferName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereOsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereOsVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click wherePathId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereRefererDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereRefererUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereToken10Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereToken1Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereToken2Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereToken3Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereToken4Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereToken5Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereToken6Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereToken7Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereToken8Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereToken9Value($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereUnique($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Click whereUserAgent($value)
 * @mixin \Eloquent
 */
class Click extends Model
{
    /**
     * Table name in database
     *
     * @var string
     */
    protected $table = 'binom_clicks';

    /**
     * Define fillable model properties
     *
     * @var array
     */
    protected $fillable = [
        "click_time",
        "lp_click",
        "unique",
        "conversion",
        "conversion_time",
        "campaign_id",
        "campaign_name",
        "path_id",
        "offer_name",
        "landing_id",
        "landing_name",
        "ip",
        "country_name",
        "country_code",
        "city_name",
        "isp_name",
        "connection_type",
        "user_agent",
        "browser",
        "browser_version",
        "device_brand",
        "device_model",
        "device_name",
        "display_size",
        "display_resolution",
        "device_pointing_method",
        "os_name",
        "os_version",
        "referer_url",
        "referer_domain",
        "language",
        "token_1_value",
        "token_2_value",
        "token_3_value",
        "token_4_value",
        "token_5_value",
        "token_6_value",
        "token_7_value",
        "token_8_value",
        "token_9_value",
        "token_10_value",
        "lead_id",
        "clickid",
        "binom_id"
    ];

    /**
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ClickCreated::class,
    ];

    /**
     * Parse and format click time
     *
     * @param int|string $time
     *
     * @return void
     */
    public function setClickTimeAttribute($time)
    {
        $this->attributes['click_time'] = Carbon::createFromTimestamp($time)->toDateTimeString();
    }

    /**
     * Parse and format converstion time
     *
     * @param int|string $time
     *
     * @return void
     */
    public function setConversionTimeAttribute($time)
    {
        $this->attributes['conversion_time'] = Carbon::createFromTimestamp($time)->toDateTimeString();
    }

    /**
     * Get Binom instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function binom()
    {
        return $this->belongsTo(Binom::class);
    }

    /**
     * Send payout amount to Binom
     *
     * @param $amount
     */
    public function sendPayout($amount)
    {
        $this->binom->sendPayout($this->clickid, $amount);
    }

    public function sendLeadReceived()
    {
        $this->binom->sendLeadReceived($this->clickid);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lead(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
