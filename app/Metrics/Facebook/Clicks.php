<?php

namespace App\Metrics\Facebook;

class Clicks extends FacebookMetric
{
    /**
     * Get metric value
     *
     * @return string|int
     */
    public function value()
    {
        return $this->insights->sum('link_clicks');
    }

    /**
     * @inheritDoc
     */
    public function metric()
    {
        return $this->value();
    }
}
