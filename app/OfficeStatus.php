<?php

namespace App;

use App\CRM\Status;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\OfficeStatus
 *
 * @property int $id
 * @property int $office_id
 * @property Status $status
 * @property bool $selectable
 * @property-read \App\Office $office
 *
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeStatus whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeStatus whereSelectable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OfficeStatus whereStatus($value)
 * @mixin \Eloquent
 */
class OfficeStatus extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'office_status';

    /**
     * Protected attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status', 'name');
    }
}
