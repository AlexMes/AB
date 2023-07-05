<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\NotificationType
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NotificationType extends Model
{
    public const ACCOUNT_STATUS_UPDATED         = 'account_status_updated';
    public const AD_STATUS_UPDATED              = 'ad_status_updated';
    public const ADSET_STATUS_UPDATED           = 'adset_status_updated';
    public const CAMPAIGN_STATUS_UPDATED        = 'campaign_status_updated';
    public const PROFILE_BANNED                 = 'profile_banned';
    public const PROFILE_RESTORED               = 'profile_restored';
    public const ACCOUNT_ADVERTISING_DISABLED   = 'account_advertising_disabled';
    public const PAYMENT_FAIL_CREATED           = 'payment_fail_created';
    public const DESTINATION_DEPOSIT            = 'destination_deposit';

    public const NOTIFICATIONS = [
        self::ACCOUNT_STATUS_UPDATED        => 'Статус аккаунта изменён',
        self::AD_STATUS_UPDATED             => 'Статус обьявления изменён',
        self::ADSET_STATUS_UPDATED          => 'Статус адсета изменён',
        self::CAMPAIGN_STATUS_UPDATED       => 'Статус кампании изменён',
        self::PROFILE_BANNED                => 'Профайл забанен',
        self::PROFILE_RESTORED              => 'Профайл восстановлен',
        self::ACCOUNT_ADVERTISING_DISABLED  => 'Рекламная функция аккаунта отключена',
        self::PAYMENT_FAIL_CREATED          => 'Сбой оплаты',
        self::DESTINATION_DEPOSIT           => 'Депозит на дестинейшене',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'denied_telegram_notification_user');
    }
}
