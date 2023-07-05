<?php

namespace App\Metrics\Facebook;

use App\Insights;
use App\Metrics\Metric;
use Illuminate\Support\Collection;

/**
 * Class TrafficMetric
 *
 * @package App\Metrics
 */
abstract class FacebookMetric extends Metric
{
    /**
     * Collection of insights
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $insights;

    /**
     * Force metric to walk through preloaded insights
     *
     * @param \Illuminate\Support\Collection $insights
     *
     */
    public function __construct(Collection $insights)
    {
        $this->insights = $insights;

        return $this;
    }

    /**
     * Named constructor
     *
     * @param \Illuminate\Support\Collection $insights
     *
     * @return \App\Metrics\Facebook\FacebookMetric
     */
    public static function make(Collection $insights)
    {
        return new static($insights);
    }
}
