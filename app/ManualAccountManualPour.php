<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\ManualAccountManualPour
 *
 * @property int $id
 * @property string $account_id
 * @property int $pour_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $status
 * @property string|null $moderation_status
 * @property-read \App\ManualAccount $account
 * @property-read \App\ManualPour $pour
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountManualPour newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountManualPour newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountManualPour query()
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountManualPour whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountManualPour whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountManualPour whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountManualPour whereModerationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountManualPour wherePourId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountManualPour whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountManualPour whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ManualAccountManualPour extends Pivot
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(ManualAccount::class, 'account_id', 'account_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pour()
    {
        return $this->belongsTo(ManualPour::class, 'pour_id', 'id');
    }
}
