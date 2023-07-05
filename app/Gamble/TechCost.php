<?php

namespace App\Gamble;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Gamble\TechCost
 *
 * @property int $id
 * @property string $date
 * @property int $user_id
 * @property string $spend
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Gamble\User $user
 *
 * @method static Builder|TechCost newModelQuery()
 * @method static Builder|TechCost newQuery()
 * @method static Builder|TechCost query()
 * @method static Builder|TechCost visible()
 * @method static Builder|TechCost whereCreatedAt($value)
 * @method static Builder|TechCost whereDate($value)
 * @method static Builder|TechCost whereId($value)
 * @method static Builder|TechCost whereSpend($value)
 * @method static Builder|TechCost whereUpdatedAt($value)
 * @method static Builder|TechCost whereUserId($value)
 * @mixin \Eloquent
 */
class TechCost extends Model
{
    /**
     * DB table name
     *
     * @var string
     */
    protected $table = 'gamble_tech_costs';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->user()->isGambler()) {
            $builder->where('user_id', auth()->id());
        }

        return $builder;
    }
}
