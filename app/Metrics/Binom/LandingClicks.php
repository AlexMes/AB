<?php

namespace App\Metrics\Binom;

class LandingClicks extends BinomMetric
{
    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->statistic()->sum('lp_clicks');
    }

    /**
     * @inheritDoc
     */
    public function metric()
    {
        return $this->value();
    }
}
