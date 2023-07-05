<?php

namespace App;

use App\Facebook\Account;
use App\Facebook\Ad;
use App\Facebook\AdSet;
use Illuminate\Database\Eloquent\Model;

/**
 * App\StoppedAdset
 *
 * @property int $id
 * @property string $account_id
 * @property string $adset_id
 * @property string $spend
 * @property string $cpl
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Account $account
 * @property-read Ad $ad
 * @property-read AdSet $adset
 *
 * @method static \Illuminate\Database\Eloquent\Builder|StoppedAdset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoppedAdset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoppedAdset query()
 * @method static \Illuminate\Database\Eloquent\Builder|StoppedAdset whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoppedAdset whereAdsetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoppedAdset whereCpl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoppedAdset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoppedAdset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoppedAdset whereSpend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StoppedAdset whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StoppedAdset extends Model
{
    protected $guarded = [];

    public function adset()
    {
        return $this->belongsTo(AdSet::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class, 'adset_id', 'adset_id');
    }
}
