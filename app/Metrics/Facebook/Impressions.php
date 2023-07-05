<?php

namespace App\Metrics\Facebook;

class Impressions extends FacebookMetric
{
    /**
     * Get metric value
     *
     * @return string|int
     */
    public function value()
    {
        return $this->insights->sum('impressions') ?? 0;
    }

    /**
     * @inheritDoc
     */
    public function metric()
    {
        return $this->value();
    }
}
