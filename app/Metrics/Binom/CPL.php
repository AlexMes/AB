<?php

namespace App\Metrics\Binom;

class CPL extends BinomMetric
{

    /**
     * Sum of expenses on ads
     *
     * @var float
     */
    protected $cost;

    /**
     * Set value of costs
     *
     * @param float $cost
     *
     * @return \App\Metrics\Binom\BinomMetric
     */
    public function useCosts($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        if (($leads = $this->statistic()->sum('leads'))) {
            return $this->cost / $leads;
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
