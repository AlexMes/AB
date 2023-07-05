<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Site
 *
 * @property int $id
 * @property int $forge_id
 * @property int $server_id
 * @property string $name
 * @property string|null $status
 * @property string|null $repository
 * @property string|null $directory
 * @property string|null $app
 * @property string|null $app_status
 * @property bool|null $has_certificates
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $certificate_status
 * @property-read \App\Server $server
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Site newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Site newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Site query()
 * @method static \Illuminate\Database\Eloquent\Builder|Site whereApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Site whereAppStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Site whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Site whereDirectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Site whereForgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Site whereHasCertificates($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Site whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Site whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Site whereRepository($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Site whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Site whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Site whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Site extends Model
{
    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'sites';

    /**
     * Unguard model
     *
     * @var array
     */
    protected $fillable = [
        'server_id',
        'name',
        'forge_id',
        'has_certificates',
        'status',
        'repository',
        'directory',
        'app',
        'app_status',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * @return string
     */
    public function getCertificateStatusAttribute()
    {
        if (is_null($this->has_certificates)) {
            return 'Не определено';
        }

        return $this->has_certificates ? 'В наличии' : 'Отсутствует';
    }
}
