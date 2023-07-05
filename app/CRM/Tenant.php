<?php

namespace App\CRM;

use App\Events\Tenants\Creating;
use App\Manager;
use App\Office;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * App\CRM\Tenant
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string $url
 * @property int $client_id
 * @property string $client_secret
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $api_token
 * @property-read \Illuminate\Database\Eloquent\Collection|Manager[] $managers
 * @property-read int|null $managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Office[] $offices
 * @property-read int|null $offices_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereClientSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereUrl($value)
 * @mixin \Eloquent
 */
class Tenant extends Authenticatable
{

    /**
     * Model table name in database
     *
     * @var string
     */
    protected $table = 'tcrm_frx_tenants';

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
        'client_id',
        'client_secret',
        'api_token',
    ];

    /**
     * @var string[]
     */
    protected $dispatchesEvents = [
        'creating' => Creating::class,
    ];

    /**
     * Use keys on routes
     *
     * @return mixed
     */
    public function getRouteKeyName()
    {
        return 'key';
    }

    /**
     * Mangers related to tenant
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function managers()
    {
        return $this->hasMany(Manager::class, 'frx_tenant_id');
    }

    /**
     * Offices related to tenant
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offices()
    {
        return $this->hasMany(Office::class, 'frx_tenant_id');
    }

    /**
     * @return $this
     */
    public function generateApiToken()
    {
        $this->api_token = Str::random(255);

        if ($this->exists) {
            $this->save();
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param string $code
     *
     * @return string
     */
    public function convertAuthorizationCodeToAccessToken(string $code)
    {
        return Http::asForm()->post(sprintf('%s/oauth/token', $this->url), [
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri'  => route('crm.auth.callback', $this),
            'code'          => $code,
        ])->throw()->offsetGet('access_token');
    }


    /**
     * get authenticated user
     *
     * @param string $token
     *
     * @return array
     */
    public function getUser(string $token)
    {
        return Http::withToken($token)->get($this->url . '/api/me')->throw()->json();
    }

    /**
     * @return $this
     */
    public function revokeApiToken()
    {
        $this->update(['api_token' => null]);

        return $this;
    }
}
