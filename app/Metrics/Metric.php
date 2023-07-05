<?php

namespace App\Metrics;

abstract class Metric
{
    /**
     * Get metric value
     *
     * @return string|null
     */
    abstract public function value();

    /**
     * Get formatted representation of metric
     *
     * @return string|null
     */
    abstract public function metric();
}
