<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\OfficePayment
 *
 * @property int $id
 * @property int $office_id
 * @property int $paid
 * @property int $assigned
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Office $office
 *
 * @method static Builder|OfficePayment completed()
 * @method static Builder|OfficePayment incomplete()
 * @method static Builder|OfficePayment newModelQuery()
 * @method static Builder|OfficePayment newQuery()
 * @method static Builder|OfficePayment query()
 * @method static Builder|OfficePayment whereAssigned($value)
 * @method static Builder|OfficePayment whereCreatedAt($value)
 * @method static Builder|OfficePayment whereId($value)
 * @method static Builder|OfficePayment whereOfficeId($value)
 * @method static Builder|OfficePayment wherePaid($value)
 * @method static Builder|OfficePayment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OfficePayment extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'office_payments';

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
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeIncomplete(Builder $builder)
    {
        return $builder->whereColumn('paid', '>', 'assigned');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeCompleted(Builder $builder)
    {
        return $builder->whereColumn('paid', '=', 'assigned');
    }
}
