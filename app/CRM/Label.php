<?php

namespace App\CRM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\CRM\Label
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CRM\LeadOrderAssignment[] $assignments
 * @property-read int|null $assignments_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Label newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Label newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Label query()
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Label whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Label extends Model
{
    /**
     * Protected attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return BelongsToMany
     */
    public function assignments()
    {
        return $this->belongsToMany(LeadOrderAssignment::class, null, null, 'assignment_id');
    }
}
