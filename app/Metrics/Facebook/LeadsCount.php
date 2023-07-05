<?php

namespace App\Metrics\Facebook;

class LeadsCount extends FacebookMetric
{
    /**
     * Get metric value
     *
     * @return string|int
     */
    public function value()
    {
        return $this->insights->sum('leads_cnt');
    }

    /**
     * {@inheritDoc}
     */
    public function metric()
    {
        return $this->value();
    }
}
