<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\BlackLead
 *
 * @property int $id
 * @property int|null $branch_id
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Branch|null $branch
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BlackLead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlackLead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlackLead query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlackLead whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlackLead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlackLead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlackLead wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlackLead whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BlackLead extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'black_leads';

    /**
     * Protected attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
