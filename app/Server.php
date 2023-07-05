<?php

namespace App;

use App\Jobs\Forge\PullSitesFromForge;
use App\Traits\AppendAccessAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Server
 *
 * @property int $id
 * @property int $forge_id
 * @property string $name
 * @property string $provider
 * @property string $ip_address
 * @property string|null $region
 * @property bool $is_ready
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $credential_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Domain[] $domains
 * @property-read int|null $domains_count
 * @property-read bool $can_create
 * @property-read bool $can_delete
 * @property-read bool $can_update
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Server newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server query()
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCredentialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereForgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereIsReady($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Server extends Model
{
    use AppendAccessAttributes;

    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'servers';

    /**
     * Unguard model
     *
     * @var array
     */
    protected $fillable = [
        'forge_id',
        'name',
        'provider',
        'region',
        'ip_address',
        'is_ready',
        'credential_id',
    ];

    /**
     * Domains placed on this server
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * Get all sites related to current server
     * and check SSL for every site
     *
     * @return $this
     */
    public function pullSites()
    {
        PullSitesFromForge::dispatch($this);

        return $this;
    }
}
