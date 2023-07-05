<?php

namespace App\Metrics\Facebook;

class Cost extends FacebookMetric
{
    /**
     * Get metric value
     *
     * @return string|int
     */
    public function value()
    {
        return $this->insights->sum('spend');
    }

    /**
     * @inheritDoc
     */
    public function metric()
    {
        return sprintf("$ %s", round($this->value(), 2));
    }
}
