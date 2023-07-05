<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\DistributionRule
 *
 * @property int $id
 * @property int|null $office_id
 * @property int|null $offer_id
 * @property string $geo
 * @property string $country_name
 * @property bool $allowed
 * @property-read mixed $allowance
 * @property-read \App\Offer|null $offer
 * @property-read \App\Office|null $office
 *
 * @method static Builder|DistributionRule allowed()
 * @method static Builder|DistributionRule denied()
 * @method static Builder|DistributionRule newModelQuery()
 * @method static Builder|DistributionRule newQuery()
 * @method static Builder|DistributionRule query()
 * @method static Builder|DistributionRule visible()
 * @method static Builder|DistributionRule whereAllowed($value)
 * @method static Builder|DistributionRule whereCountryName($value)
 * @method static Builder|DistributionRule whereGeo($value)
 * @method static Builder|DistributionRule whereId($value)
 * @method static Builder|DistributionRule whereOfferId($value)
 * @method static Builder|DistributionRule whereOfficeId($value)
 * @mixin \Eloquent
 */
class DistributionRule extends Model
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
    protected $table = 'distribution_rules';

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
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAllowed(Builder $builder)
    {
        return $builder->where('allowed', true);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeDenied(Builder $builder)
    {
        return $builder->where('allowed', false);
    }

    public function scopeVisible(Builder $builder)
    {
        if (auth()->user()->isAdmin()) {
            return $builder;
        }

        return $builder->whereIn('distribution_rules.offer_id', Offer::allowed()->pluck('id'));
    }

    /**
     * @param string $geo
     * @param int    $offerId
     *
     * @return Collection
     */
    public static function getDeniedOffices(?string $geo, int $offerId): Collection
    {
        if (!empty($geo)) {
            $globalAllowedOffices = static::allowed()->whereNotNull('office_id')->whereNull('offer_id')->get();
            $offerAllowedOffices  = static::allowed()->whereNotNull('office_id')->where('offer_id', $offerId)->get();

            return $globalAllowedOffices->pluck('office_id')
                ->when(static::isDenied($geo, $offerId), fn ($collection) => $collection->merge(Office::pluck('id')))
                // offices which have allowed countries, but not this one - denied
                // also allow offices if all are denied by geo
                ->diff($globalAllowedOffices->where('geo', $geo)->pluck('office_id'))
                // denied offices
                ->merge(
                    static::denied()
                        ->whereNotNull('office_id')
                        ->whereGeo($geo)
                        ->where(fn ($q) => $q->whereNull('offer_id')->orWhere('offer_id', $offerId))
                        ->pluck('office_id')
                )
                // deny all offer's offices(except geo in next diff())
                ->merge(
                    $offerAllowedOffices->pluck('office_id')
                    // excludes office if it's global allowed, if global has higher priority
                    /*->diff($globalAllowedOffices->where('geo', $geo)->pluck('office_id'))*/
                )
                // allow offer's + geo offices
                // also allow offices if all are denied by geo
                ->diff($offerAllowedOffices->where('geo', $geo)->pluck('office_id'))
                ->unique();
        }

        return collect();
    }

    /**
     * @param string|null $geo
     * @param int         $offerId
     *
     * @return false
     */
    public static function isDenied(?string $geo, int $offerId): bool
    {
        if (empty($geo)) {
            return false;
        }

        return static::denied()
            ->whereNull('office_id')
            ->whereGeo($geo)
            ->where(fn ($q) => $q->whereNull('offer_id')->orWhere('offer_id', $offerId))
            ->exists();
    }

    public function getAllowanceAttribute()
    {
        return $this->allowed ? 'A' : 'R';
    }
}
