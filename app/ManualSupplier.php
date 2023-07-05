<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ManualSupplier
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $branch_id
 * @property string|null $google
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ManualAccount[] $accounts
 * @property-read int|null $accounts_count
 * @property-read \App\Branch|null $branch
 *
 * @method static Builder|ManualSupplier newModelQuery()
 * @method static Builder|ManualSupplier newQuery()
 * @method static Builder|ManualSupplier query()
 * @method static Builder|ManualSupplier visible()
 * @method static Builder|ManualSupplier whereBranchId($value)
 * @method static Builder|ManualSupplier whereCreatedAt($value)
 * @method static Builder|ManualSupplier whereGoogle($value)
 * @method static Builder|ManualSupplier whereId($value)
 * @method static Builder|ManualSupplier whereName($value)
 * @method static Builder|ManualSupplier whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ManualSupplier extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'manual_suppliers';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts()
    {
        return $this->hasMany(ManualAccount::class, 'supplier_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        if (auth()->user()->isAdmin() || auth()->user()->isDeveloper()) {
            return $builder;
        }

        if (auth()->user()->branch_id === 19) {
            return $builder->where('manual_suppliers.branch_id', auth()->user()->branch_id);
        }

        return $builder->where('manual_suppliers.branch_id', auth()->user()->branch_id)
            ->orWhereNull('manual_suppliers.branch_id');
    }
}
