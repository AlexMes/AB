<?php

namespace App\Metrics\Binom;

use App\Binom\Statistic;

class Clicks extends BinomMetric
{
    /**
     * @inheritDoc
     */
    public function value()
    {
        return Statistic::forOffers($this->offers)
            ->allowedOffers()
            ->whereBetween('date', [$this->since->toDateString(), $this->until->toDateString()])
            ->sum('unique_clicks');
    }

    /**
     * @inheritDoc
     */
    public function metric()
    {
        return $this->value();
    }
}
