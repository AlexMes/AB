<?php

namespace App\Facebook;

use App\Facebook\Events\PaymentFails\Created;
use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Facebook\PaymentFail
 *
 * @property int $id
 * @property string|null $account_id
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $card
 * @property-read \App\Facebook\Account|null $account
 * @property-read User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentFail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentFail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentFail query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentFail whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentFail whereCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentFail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentFail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentFail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentFail whereUserId($value)
 * @mixin \Eloquent
 */
class PaymentFail extends Model
{
    /**
     * Table name in database
     *
     * @var string
     */
    protected $table = 'facebook_payment_fails_log';

    /**
     * Attributes protected from mass-assignment
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Bind model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created'   => Created::class,
    ];

    /**
     * Related ads account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    /**
     * Related user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
