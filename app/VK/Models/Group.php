<?php

namespace App\VK\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\VK\Models\Group
 *
 * @property int $id
 * @property string $name
 * @property string $vk_id
 * @property int $profile_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\VK\Models\Profile $profile
 *
 * @method static Builder|Group newModelQuery()
 * @method static Builder|Group newQuery()
 * @method static Builder|Group query()
 * @method static Builder|Group visible()
 * @method static Builder|Group whereCreatedAt($value)
 * @method static Builder|Group whereId($value)
 * @method static Builder|Group whereName($value)
 * @method static Builder|Group whereProfileId($value)
 * @method static Builder|Group whereUpdatedAt($value)
 * @method static Builder|Group whereVkId($value)
 * @mixin \Eloquent
 */
class Group extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vk_groups';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        return $builder->whereHas('profile', fn ($query) => $query->visible());
    }
}
