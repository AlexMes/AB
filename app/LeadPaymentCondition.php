<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LeadPaymentCondition
 *
 * @property int $id
 * @property int $office_id
 * @property int $offer_id
 * @property string $model
 * @property string $cost
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Offer $offer
 * @property-read \App\Office $office
 *
 * @method static Builder|LeadPaymentCondition cpa()
 * @method static Builder|LeadPaymentCondition cpl()
 * @method static Builder|LeadPaymentCondition newModelQuery()
 * @method static Builder|LeadPaymentCondition newQuery()
 * @method static Builder|LeadPaymentCondition query()
 * @method static Builder|LeadPaymentCondition whereCost($value)
 * @method static Builder|LeadPaymentCondition whereCreatedAt($value)
 * @method static Builder|LeadPaymentCondition whereId($value)
 * @method static Builder|LeadPaymentCondition whereModel($value)
 * @method static Builder|LeadPaymentCondition whereOfferId($value)
 * @method static Builder|LeadPaymentCondition whereOfficeId($value)
 * @method static Builder|LeadPaymentCondition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LeadPaymentCondition extends Model
{
    public const CPA = 'cpa';
    public const CPL = 'cpl';

    public const MODELS = [
        self::CPA,
        self::CPL,
    ];

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'lead_payment_conditions';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @param int|null $offerId
     * @param int|null $officeId
     *
     * @return string|null
     */
    public static function getCpa(?int $offerId, ?int $officeId): ?string
    {
        return optional(
            static::cpa()
                ->whereOfferId($offerId)
                ->whereOfficeId($officeId)
                ->first()
        )->cost;
    }

    /**
     * @param int|null $offerId
     * @param int|null $officeId
     *
     * @return string|null
     */
    public static function getCpl(?int $offerId, ?int $officeId): ?string
    {
        return optional(
            static::cpl()
                ->whereOfferId($offerId)
                ->whereOfficeId($officeId)
                ->first()
        )->cost;
    }

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
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeCpa(Builder $builder)
    {
        return $builder->where('model', self::CPA);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeCpl(Builder $builder)
    {
        return $builder->where('model', self::CPL);
    }
}
