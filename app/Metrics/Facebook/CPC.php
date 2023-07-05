<?php

namespace App\Metrics\Facebook;

class CPC extends FacebookMetric
{
    /**
     * Get metric value
     *
     * @return string|int
     */
    public function value()
    {
        if (($clicks = Clicks::make($this->insights)->value())) {
            return Cost::make($this->insights)->value() / $clicks;
        }

        return 0;
    }

    /**
     * @inheritDoc
     */
    public function metric()
    {
        return sprintf("$ %s", round($this->value(), 2));
    }
}
