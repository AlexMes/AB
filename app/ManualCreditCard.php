<?php

namespace App;

use App\Casts\Encrypted;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ManualCreditCard
 *
 * @property int $id
 * @property string $digits
 * @property mixed $number
 * @property string $bank_name
 * @property string|null $social_profile
 * @property int $buyer_id
 * @property string $account_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\ManualAccount $account
 * @property-read \App\User $buyer
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ManualCreditCard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManualCreditCard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManualCreditCard query()
 * @method static \Illuminate\Database\Eloquent\Builder|ManualCreditCard whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualCreditCard whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualCreditCard whereBuyerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualCreditCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualCreditCard whereDigits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualCreditCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualCreditCard whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualCreditCard whereSocialProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualCreditCard whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ManualCreditCard extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'manual_credit_cards';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'number',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'number'     => Encrypted::class,
    ];

    /**
     * Bind listeners
     *
     * @return void
     */
    public static function booted()
    {
        static::saving(
            function ($model) {
                $clearNumber = preg_replace('/[^0-9]/', '', $model->number);

                $model->digits = substr($clearNumber, strlen($clearNumber) - 4);
            }
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(ManualAccount::class, 'account_id', 'account_id');
    }
}
