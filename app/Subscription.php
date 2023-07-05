<?php

namespace App;

use Cron\CronExpression;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Subscription
 *
 * @property int $id
 * @property string $service
 * @property string $account
 * @property bool $enabled
 * @property string|null $amount
 * @property string $frequency
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Subscription enabled()
 * @method static Builder|Subscription newModelQuery()
 * @method static Builder|Subscription newQuery()
 * @method static Builder|Subscription query()
 * @method static Builder|Subscription whereAccount($value)
 * @method static Builder|Subscription whereAmount($value)
 * @method static Builder|Subscription whereCreatedAt($value)
 * @method static Builder|Subscription whereEnabled($value)
 * @method static Builder|Subscription whereFrequency($value)
 * @method static Builder|Subscription whereId($value)
 * @method static Builder|Subscription whereService($value)
 * @method static Builder|Subscription whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Subscription extends Model
{
    /**
     * Model table name in DB
     *
     * @var string
     */
    protected $table = 'subscriptions';

    /**
     * Protected model attributes
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Determine is subscription enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled === true;
    }

    /**
     * Limit subscriptions to enabled
     *
     * @param Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnabled(Builder $builder)
    {
        return $builder->where('enabled', true);
    }

    /**
     * Determines is subscription is due
     * in current week
     *
     * @return bool
     */
    public function dueThisWeek()
    {
        return $this->nextRunAt()->isCurrentWeek();
    }

    /**
     * Determines is subscription
     * is due tomorrow
     *
     * @return bool
     */
    public function dueTomorrow()
    {
        return $this->nextRunAt()->isTomorrow();
    }

    /**
     * Determines is subscription
     * is due today
     *
     * @return bool
     */
    public function dueToday()
    {
        return $this->nextRunAt()->isToday();
    }

    /**
     * Determine is subscription should run right now
     *
     * @return \Illuminate\Support\Carbon
     */
    public function nextRunAt()
    {
        return Carbon::parse(CronExpression::factory($this->frequency)->getNextRunDate(now()));
    }

    /**
     * @return bool
     */
    public function dueInNearFuture(): bool
    {
        // Skip upcoming payments due on week, when today is not Monday.
        return now()->isMonday() ? $this->dueThisWeek() : false || $this->dueTomorrow() || $this->dueToday();
    }
}
