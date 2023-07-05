<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

/**
 * App\Proxy
 *
 * @property int $id
 * @property string $name
 * @property string|null $login
 * @property string|null $password
 * @property string $protocol
 * @property string $host
 * @property string $port
 * @property string|null $geo
 * @property int|null $branch_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_active
 * @property-read \App\Branch|null $branch
 * @property-read string $credentials_url
 *
 * @method static Builder|Proxy active()
 * @method static Builder|Proxy newModelQuery()
 * @method static Builder|Proxy newQuery()
 * @method static Builder|Proxy query()
 * @method static Builder|Proxy whereBranchId($value)
 * @method static Builder|Proxy whereCreatedAt($value)
 * @method static Builder|Proxy whereGeo($value)
 * @method static Builder|Proxy whereHost($value)
 * @method static Builder|Proxy whereId($value)
 * @method static Builder|Proxy whereIsActive($value)
 * @method static Builder|Proxy whereLogin($value)
 * @method static Builder|Proxy whereName($value)
 * @method static Builder|Proxy wherePassword($value)
 * @method static Builder|Proxy wherePort($value)
 * @method static Builder|Proxy whereProtocol($value)
 * @method static Builder|Proxy whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Proxy extends Model
{
    public const SOCKS5 = 'socks5';
    public const HTTP   = 'http';

    public const PROTOCOLS = [
        self::SOCKS5,
        self::HTTP,
    ];

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'proxies';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return string
     */
    public function getCredentialsUrlAttribute(): string
    {
        return $this->login !== null
        ? sprintf('%s://%s:%s@%s:%s', $this->protocol, $this->login, $this->password, $this->host, $this->port)
        : sprintf('%s://%s:%s', $this->protocol, $this->host, $this->port);
    }

    /**
     * @return array
     */
    public function checkConnection(string $serverIp = null): array
    {
        if ($serverIp === null) {
            try {
                $response = Http::withOptions([
                    'connect_timeout' => 3,
                ])->get('https://ifconfig.me/ip');

                $serverIp = trim($response->body());
            } catch (\Throwable $exception) {
                //
            }
        }

        try {
            $response = Http::withOptions([
                'proxy'           => $this->credentials_url,
                'connect_timeout' => 5,
            ])->get('https://ifconfig.me/ip');

            $proxyIp = trim($response->body());
        } catch (\Throwable $exception) {
            $details = $exception->getMessage();
        }

        return [
            'server_ip' => $serverIp ?? null,
            'proxy_ip'  => $proxyIp ?? null,
            'details'   => $details ?? null,
            'active'    => isset($proxyIp) && $proxyIp != $serverIp,
        ];
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeActive(Builder $builder)
    {
        return $builder->where('is_active', true);
    }
}
