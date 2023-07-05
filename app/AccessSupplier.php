<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AccessSupplier
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Access[] $accesses
 * @property-read int|null $accesses_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AccessSupplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessSupplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessSupplier query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessSupplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessSupplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessSupplier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessSupplier whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AccessSupplier extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table   = 'access_suppliers';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accesses()
    {
        return $this->hasMany(Access::class, 'supplier_id');
    }
}
