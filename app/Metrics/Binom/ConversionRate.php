<?php

namespace App\Metrics\Binom;

class ConversionRate extends BinomMetric
{

    /**
     * @inheritDoc
     */
    public function value()
    {
        $stats = $this->statistic()->get(['clicks', 'leads']);

        if ($stats->sum('clicks')) {
            return ($stats->sum('leads') / $stats->sum('clicks')) * 100;
        }

        return 0;
    }

    /**
     * @inheritDoc
     */
    public function metric()
    {
        return sprintf("%s %%", round($this->value(), 2));
    }
}
