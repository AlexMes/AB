<?php

namespace App\Metrics\Binom;

class Leads extends BinomMetric
{

    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->statistic()->sum('leads');
    }

    /**
     * @inheritDoc
     */
    public function metric()
    {
        return $this->value();
    }
}
