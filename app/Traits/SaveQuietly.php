<?php

namespace App\Traits;

trait SaveQuietly
{
    /**
     * Save without firing events
     *
     * @param $options
     *
     * @return mixed
     */
    public function saveQuietly($options = [])
    {
        return static::withoutEvents(function () use ($options) {
            return $this->save($options);
        });
    }
}
