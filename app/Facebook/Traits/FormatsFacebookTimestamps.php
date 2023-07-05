<?php

namespace App\Facebook\Traits;

use Carbon\Carbon;

/**
 * Trait FormatsFacebookTimestamps
 *
 * @package App\Facebook\Traits
 * @mixin \Eloquent
 */
trait FormatsFacebookTimestamps
{
    /**
     * Format Facebook's date-time to date time
     *
     *
     * @param string $value
     *
     * @return void
     */
    public function setCreatedTimeAttribute($value)
    {
        try {
            $this->attributes['created_time'] = Carbon::parse($value)->toDateTimeString() ?? null;
        } catch (\Throwable $exception) {
            $this->attributes['created_time'] =  null;
        }
    }

    /**
     * Format Facebook's date-time to date time
     *
     *
     * @param string $value
     *
     * @return void
     */
    public function setStartTimeAttribute($value)
    {
        try {
            $this->attributes['start_time'] = Carbon::parse($value)->toDateTimeString() ?? null;
        } catch (\Throwable $exception) {
            $this->attributes['start_time'] =  null;
        }
    }

    /**
     * Format Facebook's date-time to date time
     *
     *
     * @param string $value
     *
     * @return void
     */
    public function setUpdatedTimeAttribute($value)
    {
        try {
            $this->attributes['updated_time'] = Carbon::parse($value)->toDateTimeString() ?? null;
        } catch (\Throwable $exception) {
            $this->attributes['updated_time'] =  null;
        }
    }

    /**
     * Format Facebook's date-time to date time
     *
     *
     * @param string $value
     *
     * @return void
     */
    public function setTimeStartAttribute($value)
    {
        try {
            $this->attributes['time_start'] = Carbon::parse($value)->toDateTimeString() ?? null;
        } catch (\Throwable $exception) {
            $this->attributes['time_start'] =  null;
        }
    }

    /**
     * Format Facebook's date-time to date time
     *
     * @param string $value
     *
     * @return void
     */
    public function setTimeStopAttribute($value)
    {
        try {
            $this->attributes['time_stop'] = Carbon::parse($value)->toDateTimeString() ?? null;
        } catch (\Throwable $exception) {
            $this->attributes['time_stop'] =  null;
        }
    }
}
