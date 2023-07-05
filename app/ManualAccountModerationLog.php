<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ManualAccountModerationLog
 *
 * @property int $id
 * @property string $account_id
 * @property string|null $original
 * @property string $changed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountModerationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountModerationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountModerationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountModerationLog whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountModerationLog whereChanged($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountModerationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountModerationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountModerationLog whereOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManualAccountModerationLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ManualAccountModerationLog extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'manual_account_moderation_logs';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];
}
