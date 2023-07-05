<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\OfficeGroup
 *
 * @property int $id
 * @property string $name
 * @property int $branch_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Branch $branch
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Office[] $offices
 * @property-read int|null $offices_count
 *
 * @method static Builder|OfficeGroup newModelQuery()
 * @method static Builder|OfficeGroup newQuery()
 * @method static Builder|OfficeGroup query()
 * @method static Builder|OfficeGroup visible()
 * @method static Builder|OfficeGroup whereBranchId($value)
 * @method static Builder|OfficeGroup whereCreatedAt($value)
 * @method static Builder|OfficeGroup whereId($value)
 * @method static Builder|OfficeGroup whereName($value)
 * @method static Builder|OfficeGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OfficeGroup extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'office_groups';

    /**
     * Protected attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function offices()
    {
        return $this->belongsToMany(Office::class, null, 'group_id');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (app()->runningInConsole() || auth()->user()->isAdmin()) {
            return $builder;
        }

        return $builder->where('office_groups.branch_id', auth()->user()->branch_id);
    }
}
