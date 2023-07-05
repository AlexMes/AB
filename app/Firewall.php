<?php

namespace App;

use App\Events\FirewallRuleCreated;
use App\Events\FirewallRuleDeleting;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Firewall
 *
 * @property int $id
 * @property int $user_id
 * @property string $ip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Firewall newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Firewall newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Firewall query()
 * @method static \Illuminate\Database\Eloquent\Builder|Firewall whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Firewall whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Firewall whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Firewall whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Firewall whereUserId($value)
 * @mixin \Eloquent
 */
class Firewall extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'firewall_rules';

    /**
     * Map model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created'  => FirewallRuleCreated::class,
        'deleting' => FirewallRuleDeleting::class
    ];

    /**
     * Gets permission's user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
