<?php

namespace App\Gamble;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Gamble\Campaign
 *
 * @property int $id
 * @property string $campaign_id
 * @property string $name
 * @property int $account_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $offer_id
 * @property-read \App\Gamble\Account $account
 * @property-read \App\Gamble\Offer|null $offer
 *
 * @method static Builder|Campaign newModelQuery()
 * @method static Builder|Campaign newQuery()
 * @method static Builder|Campaign query()
 * @method static Builder|Campaign visible()
 * @method static Builder|Campaign whereAccountId($value)
 * @method static Builder|Campaign whereCampaignId($value)
 * @method static Builder|Campaign whereCreatedAt($value)
 * @method static Builder|Campaign whereId($value)
 * @method static Builder|Campaign whereName($value)
 * @method static Builder|Campaign whereOfferId($value)
 * @method static Builder|Campaign whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Campaign extends Model
{
    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'gamble_campaigns';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function scopeVisible(Builder $builder)
    {
        if (auth()->user()->isGambler()) {
            $builder->whereHas('account', fn ($query) => $query->where('user_id', auth()->id()));
        }

        return $builder;
    }
}
