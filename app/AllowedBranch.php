<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\AllowedBranch
 *
 * @property int $id
 * @property int $user_id
 * @property int $branch_id
 * @property-read \App\Branch $branch
 * @property-read \App\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedBranch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedBranch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedBranch query()
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedBranch whereBranchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedBranch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AllowedBranch whereUserId($value)
 * @mixin \Eloquent
 */
class AllowedBranch extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'allowed_branches';

    /**
     * Protected attributes
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
