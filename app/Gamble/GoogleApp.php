<?php

namespace App\Gamble;

use App\Gamble\Events\GoogleAppUpdated;
use App\Traits\RecordEvents;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Gamble\GoogleApp
 *
 * @property int $id
 * @property string $name
 * @property string $market_id
 * @property bool $enabled
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $geo
 * @property string|null $fb_app_id
 * @property string|null $fb_app_secret
 * @property string|null $fb_token
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Support\Collection $countries
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Gamble\GoogleAppHit[] $hits
 * @property-read int|null $hits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Gamble\GoogleAppLink[] $links
 * @property-read int|null $links_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp whereFbAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp whereFbAppSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp whereFbToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp whereGeo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp whereMarketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleApp whereUrl($value)
 * @mixin \Eloquent
 */
class GoogleApp extends Model
{
    use RecordEvents;

    public const DISABLED = 'disabled';
    public const ENABLED  = 'enabled';

    /**
     * Protect attributes from mass-assignment
     *
     * @var string[]
     */
    protected $guarded = ['id'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['countries'];

    /**
     * Bind events
     *
     * @var string[]
     */
    protected $dispatchesEvents = [
        'updated' => GoogleAppUpdated::class
    ];

    /**
     * Enable in-app webview
     *
     * @return bool
     */
    public function enable()
    {
        return $this->recordAs(self::ENABLED)->update([
            'enabled' => true,
        ]);
    }

    /**
     * Disable in-app webview
     *
     * @return bool
     */
    public function disable()
    {
        return $this->recordAs(self::DISABLED)->update([
            'enabled' => false,
        ]);
    }

    /**
     * Hits for certain app
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hits()
    {
        return $this->hasMany(GoogleAppHit::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function links()
    {
        return $this->hasMany(GoogleAppLink::class, 'app_id', 'id');
    }

    /**
     * Countries for certain app from geo codes.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCountriesAttribute()
    {
        $codes = !empty($this->attributes['geo']) ? explode(',', $this->attributes['geo']) : [];

        return Country::all()
            ->filter(fn (Country $country) => in_array($country->code, $codes))
            ->flatten();
    }
}
