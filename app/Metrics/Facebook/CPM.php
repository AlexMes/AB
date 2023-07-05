<?php

namespace App\Metrics\Facebook;

class CPM extends FacebookMetric
{
    /**
     * Get metric value
     *
     * @return string|int
     */
    public function value()
    {
        if (($impressions = Impressions::make($this->insights)->value())) {
            return Cost::make($this->insights)->value() / ($impressions / 1000);
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
