<?php

namespace App\Binom;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Binom\Campaign
 *
 * @property int $id
 * @property string $name
 * @property int|null $offer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $ts_id
 * @property int|null $binom_id
 * @property int|null $campaign_id
 * @property-read \App\Binom|null $binom
 * @property-read \App\Offer|null $offer
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign query()
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereBinomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereTsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campaign whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Campaign extends Model
{
    /**
     * Table name in database
     *
     * @var string
     */
    protected $table = 'binom_campaigns';

    /**
     * Guard attributes from mass-assignment
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Binom instance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function binom()
    {
        return $this->belongsTo(\App\Binom::class);
    }

    /**
     * Offer instance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offer()
    {
        return $this->belongsTo(\App\Offer::class);
    }

    /**
     * Collect campaign statistics from service
     *
     * @param null $date
     *
     * @throws \App\Binom\Exceptions\BinomReponseException
     *
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    public function getStatistics($date = null)
    {
        $date = $date ?? now();

        return collect($this->binom->getStatistics($this, [
            'group1'   => 287,
            'group2'   => 285,
            'group3'   => 289,
            'timezone' => '+3',
            'date_s'   => $date->toDateString(),
            'date_e'   => $date->toDateString(),
        ]));
    }
}
