<?php

namespace App\Traits;

use App\Event;
use Illuminate\Database\Eloquent\Model;

trait RecordEvents
{
    /**
     * Event type slug
     *
     * @var string|null
     */
    protected $eventType = null;

    /**
     * Bootstrap logging listeners
     *
     * @return void
     */
    protected static function bootRecordEvents()
    {
        static::created(fn (Model $model) => Event::useType($model, $model->eventType ?? 'created')->save());

        static::updated(fn (Model $model) => Event::useType($model, $model->eventType ?? 'updated')->save());

        static::deleting(fn (Model $model) => Event::useType($model, $model->eventType ?? 'deleted')->save());
    }

    /**
     * @param string|null $eventType
     *
     * @return $this
     */
    public function recordAs(?string $eventType)
    {
        $this->eventType = $eventType;

        return $this;
    }

    /**
     * @param string     $eventType
     * @param array|null $payload
     * @param array|null $original
     *
     * @return \App\Event|bool
     */
    public function addEvent(string $eventType, array $payload = null, array $original = null)
    {
        /** @var Model $this */
        $event                = Event::useType($this, $eventType);
        $event->custom_data   = $payload;
        $event->original_data = $original ?? $event->original_data;

        return tap($event)->save();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function events()
    {
        return $this->morphMany(Event::class, 'eventable');
    }
}
