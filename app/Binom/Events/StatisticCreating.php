<?php

namespace App\Binom\Events;

use App\Binom\Statistic;

class StatisticCreating
{
    /**
     * @var \App\Binom\Statistic
     */
    private $statistic;

    /**
     * StatisticCreated constructor.
     *
     * @param \App\Binom\Statistic $statistic
     */
    public function __construct(Statistic $statistic)
    {
        $this->statistic = $statistic;
    }
}
