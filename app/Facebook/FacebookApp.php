<?php

namespace App\Facebook;

use FacebookAds\Api;
use FacebookAds\Object\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * App\Facebook\FacebookApp
 *
 * @property string $id
 * @property string $secret
 * @property string|null $default_token
 * @property string $domain
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $order
 * @property-read string $callback_route
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FacebookApp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FacebookApp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FacebookApp query()
 * @method static \Illuminate\Database\Eloquent\Builder|FacebookApp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacebookApp whereDefaultToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacebookApp whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacebookApp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacebookApp whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacebookApp whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacebookApp whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FacebookApp whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FacebookApp extends Model
{
    /**
     * Table name in database
     *
     * @var string
     */
    protected $table = 'facebook_apps';

    /**
     * Disable incrementing primary key
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Hide attributes from JSON
     *
     * @var array
     */
    protected $hidden = [
        'secret',
        'default_token',
    ];

    /**
     * @throws \Facebook\Exceptions\FacebookSDKException
     *
     * @return \Facebook\Facebook
     */
    public function facebook(): \Facebook\Facebook
    {
        return new \Facebook\Facebook([
            'app_id'                => $this->id,
            'app_secret'            => $this->secret,
            'default_graph_version' => config('facebook.default_graph_version'),
            'default_access_token'  => $this->default_token,
        ]);
    }

    /**
     * @param string|null $token
     *
     * @return Api|null
     */
    public function api(string $token = null)
    {
        Api::init($this->id, $this->secret, $token ?? $this->default_token);

        return Api::instance();
    }

    /**
     * @return string
     */
    public function getCallbackRouteAttribute()
    {
        return sprintf('https://%s%s', $this->domain, route('facebook.callback', [], false));
    }

    /**
     * @param string $token
     *
     * @return Application
     */
    public function instance(string $token): Application
    {
        $this->api($token);

        return new Application($this->id);
    }

    /**
     * Pick up instance from cache or set up one
     *
     * @param string $id
     *
     * @throws ModelNotFoundException
     *
     * @return FacebookApp
     */
    public static function cache(string $id): FacebookApp
    {
        return Cache::remember("fb-app-{$id}", now()->addDays(7), function () use ($id) {
            return self::findOrFail($id);
        });
    }

    /**
     * Resolve instance by FB Account or id
     *
     * @param string|Profile $param
     *
     * @return FacebookApp
     */
    public static function init($param): FacebookApp
    {
        return self::cache($param->app_id ?? $param);
    }

    /**
     * Build Facebook http client
     *
     * @param string|Profile $param
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     *
     * @return \Facebook\Facebook
     */
    public static function build($param): \Facebook\Facebook
    {
        return self::init($param)->facebook();
    }

    /**
     * Initialize and connect Facebook Marketing api
     *
     * @param string|Profile $param
     * @param string|null    $token
     *
     * @return Api|null
     */
    public static function initApi($param, string $token = null)
    {
        return self::init($param)->api($param->token ?? $token);
    }

    /**
     * Forget current app from cache
     *
     * @param string $id
     *
     * @return bool
     */
    public static function forget(string $id): bool
    {
        return Cache::forget(sprintf("fb-app-%s", $id));
    }

    /**
     * Get app instance from request state
     *
     * @param Request $request
     *
     * @return FacebookApp
     */
    public static function fromRequest(Request $request): FacebookApp
    {
        return self::cache(json_decode($request->get('state'), true)['app']);
    }

    /**
     * Get Facebook php-sdk Application instance
     *
     * @see Application
     *
     * @param string|Profile $param
     * @param string|null    $token
     *
     * @return Application
     */
    public static function initInstance($param, string $token = null): Application
    {
        return self::init($param)->instance($param->token ?? $token);
    }

    /**
     * Get current Facebook app in order to specific APP_URL
     * remember each profile has different app
     *
     * @throws \Throwable
     *
     * @return FacebookApp
     */
    public static function current()
    {
        throw_unless(($domain = explode('://', config('app.url'))[1] ?? null), new \Exception('Set up APP_URL key'));

        return self::where('domain', $domain)->firstOrFail();
    }
}
