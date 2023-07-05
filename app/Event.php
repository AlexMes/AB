<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * App\Event
 *
 * @property int $id
 * @property int $eventable_id
 * @property string $eventable_type
 * @property string $type
 * @property array|null $original_data
 * @property array|null $changed_data
 * @property array|null $custom_data
 * @property int|null $auth_id
 * @property string|null $auth_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $auth
 * @property-read Model|\Eloquent $eventable
 * @property-read mixed $changes
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereAuthId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereAuthType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereChangedData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCustomData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEventableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEventableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereOriginalData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Event extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'original_data' => 'json',
        'changed_data'  => 'json',
        'custom_data'   => 'json',
    ];

    protected $appends = ['changes'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function eventable()
    {
        return $this->morphTo();
    }

    /**
     * Gets auth user(User/Manager)
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function auth()
    {
        return $this->morphTo(null, 'auth_type', 'auth_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Event $event) {
            $event->auth_id     = auth()->id();
            $event->auth_type   = auth()->user() ? get_class(auth()->user()) : null;
        });
    }

    /**
     * @param Model  $model
     * @param string $type
     *
     * @return Event
     */
    public static function useType(Model $model, $type)
    {
        $event = new Event([
            'type'          => $type,
            'original_data' => $type === 'deleted'
                ? $model->getOriginal()
                : Arr::except(
                    array_intersect_key($model->getOriginal(), $model->getDirty()),
                    ['updated_at', 'created_at']
                ),
            'changed_data'  => Arr::except($model->getDirty(), ['updated_at', 'created_at']),
        ]);

        $event->eventable()->associate($model);

        $model->recordAs(null);

        return $event;
    }

    public function getChangesAttribute()
    {
        return collect(array_merge($this->changed_data, $this->custom_data ?? []))
            ->map(function ($value, $field) {
                return [
                    'original' => $this->original_data[$field] ?? '',
                    'changed'  => $value,
                ];
            });
    }
}
