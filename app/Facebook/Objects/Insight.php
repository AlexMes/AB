<?php

namespace App\Facebook\Objects;

/**
 * Class Insight
 *
 * @property string $name
 *
 * @package App\Facebook\Objects
 */
class Insight
{
    /**
     * Insight constructor.
     *
     * @param \FacebookAds\Object\AdsInsights $result
     *
     * @return void
     */
    public function __construct($result)
    {
        $this->fill($result->exportAllData());
    }

    /**
     * Fill the resource with the array of attributes
     *
     * @param mixed $attributes
     *
     * @return void
     */
    private function fill($attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($key === "insights") {
                $this->insights = $value['data'][0];
            } else {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Lol, what a shame, this work, and
     * i given zero fucks for quality now.
     *
     * @param string $name
     *
     * @return null
     */
    public function __get($name)
    {
        return null;
    }

    /**
     * @return int
     */
    public function leads()
    {
        return collect($this->insights['actions'])->filter(function ($action) {
            return $action['action_type'] === 'lead';
        })->first()['value'] ?? 0;
    }

    /**
     * Get cost per result
     */
    public function cpr()
    {
        return collect($this->insights['cost_per_action_type'])->filter(function ($action) {
            return $action['action_type'] === 'lead';
        })->first()['value'] ?? 0;
    }
}
