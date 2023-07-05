<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Page
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string|null $repository
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder|Page black()
 * @method static Builder|Page newModelQuery()
 * @method static Builder|Page newQuery()
 * @method static Builder|Page query()
 * @method static Builder|Page safe()
 * @method static Builder|Page whereCreatedAt($value)
 * @method static Builder|Page whereId($value)
 * @method static Builder|Page whereName($value)
 * @method static Builder|Page whereRepository($value)
 * @method static Builder|Page whereType($value)
 * @method static Builder|Page whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Page extends Model
{
    public const SAFE  = 'safe';
    public const BLACK = 'black';

    /**
     * Database table name
     *
     * @var string
     */
    protected $table = 'pages';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Scope for black pages
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeBlack(Builder $builder)
    {
        $builder->where('type', self::BLACK);
    }

    /**
     * Scope for safe pages
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeSafe(Builder $builder)
    {
        $builder->where('type', self::SAFE);
    }
}
