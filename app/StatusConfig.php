<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\StatusConfig
 *
 * @property int $id
 * @property int $office_id
 * @property int $assigned_days_ago
 * @property string $new_status
 * @property array $statuses
 * @property string $statuses_type
 * @property bool $enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|StatusConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatusConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StatusConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|StatusConfig whereAssignedDaysAgo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusConfig whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusConfig whereNewStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusConfig whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusConfig whereStatuses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusConfig whereStatusesType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StatusConfig whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StatusConfig extends Model
{
    public const IN  = 'in';
    public const OUT = 'out';

    public const TYPES = [
        self::IN,
        self::OUT,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'status_configs';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'statuses' => 'json',
    ];
}
