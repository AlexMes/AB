<?php

namespace App\VK\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\VK\Models\Form
 *
 * @property int $id
 * @property string $name
 * @property string $vk_id
 * @property string $vk_group_id
 * @property array|null $questions
 * @property array|null $raw_data
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\VK\Models\Group $group
 * @property-read \App\VK\Models\Profile|null $profile
 *
 * @method static Builder|Form active()
 * @method static Builder|Form newModelQuery()
 * @method static Builder|Form newQuery()
 * @method static Builder|Form query()
 * @method static Builder|Form visible()
 * @method static Builder|Form whereCreatedAt($value)
 * @method static Builder|Form whereId($value)
 * @method static Builder|Form whereIsActive($value)
 * @method static Builder|Form whereName($value)
 * @method static Builder|Form whereQuestions($value)
 * @method static Builder|Form whereRawData($value)
 * @method static Builder|Form whereUpdatedAt($value)
 * @method static Builder|Form whereVkGroupId($value)
 * @method static Builder|Form whereVkId($value)
 * @mixin \Eloquent
 */
class Form extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vk_forms';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'questions' => 'json',
        'raw_data'  => 'json',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function profile()
    {
        return $this->hasOneThrough(
            Profile::class,
            Group::class,
            'vk_id',
            'id',
            'vk_group_id',
            'profile_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'vk_group_id', 'vk_id');
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

    /**
     * @param Builder $builder
     *
     * @return mixed
     */
    public function scopeActive(Builder $builder)
    {
        return $builder->whereIsActive(true);
    }
}
