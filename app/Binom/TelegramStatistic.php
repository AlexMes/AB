<?php

namespace App\Binom;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Binom\TelegramStatistic
 *
 * @property string $date
 * @property int $campaign_id
 * @property int $clicks
 * @property int $lp_clicks
 * @property int $lp_views
 * @property int $unique_clicks
 * @property int $unique_camp_clicks
 * @property int $leads
 * @property string|null $utm_source
 * @property string|null $utm_campaign
 * @property string|null $fb_campaign_id
 * @property string|null $account_id
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic query()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereFbCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereLeads($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereLpClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereLpViews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereUniqueCampClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereUniqueClicks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereUtmCampaign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramStatistic whereUtmSource($value)
 * @mixin \Eloquent
 */
class TelegramStatistic extends Model
{
    /**
     * @var string
     */
    protected $table = 'binom_telegram_statistics';

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * No incrementing whatever.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * No primary key
     *
     * @var null
     */
    protected $primaryKey = null;
}
