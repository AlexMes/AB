<?php

namespace App;

use App\Deluge\Events\Apps\Saved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\ManualApp
 *
 * @property int $id
 * @property string $link
 * @property string $status
 * @property string|null $chat_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder|ManualApp newModelQuery()
 * @method static Builder|ManualApp newQuery()
 * @method static Builder|ManualApp query()
 * @method static Builder|ManualApp visible()
 * @method static Builder|ManualApp whereChatId($value)
 * @method static Builder|ManualApp whereCreatedAt($value)
 * @method static Builder|ManualApp whereId($value)
 * @method static Builder|ManualApp whereLink($value)
 * @method static Builder|ManualApp whereStatus($value)
 * @method static Builder|ManualApp whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ManualApp extends Model
{
    public const NEW       = 'new';
    public const PUBLISHED = 'published';
    public const BANNED    = 'banned';

    public const STATUSES = [
        self::NEW,
        self::PUBLISHED,
        self::BANNED,
    ];

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'manual_apps';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Map model events
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saved' => Saved::class,
    ];

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVisible(Builder $builder)
    {
        return $builder;
    }
}
