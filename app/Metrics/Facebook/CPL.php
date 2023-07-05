<?php

namespace App\Metrics\Facebook;

class CPL extends FacebookMetric
{
    /**
     * Get metric value
     *
     * @return string|int
     */
    public function value()
    {
        if (($leadsCount = LeadsCount::make($this->insights)->value())) {
            return Cost::make($this->insights)->value() / $leadsCount;
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
