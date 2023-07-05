<?php

namespace App\Metrics\Binom;

use Illuminate\Database\Eloquent\Builder;

class LandingConversion extends BinomMetric
{

    /**
     * @var string
     */
    protected $campaign;

    /**
     * Limit data to specific campaign
     *
     * @param string $campaign
     *
     * @return \App\Metrics\Binom\LandingConversion
     */
    public function forCampaign(string $campaign)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        $stats = $this->statistic()->when($this->campaign, function (Builder $query) {
            return $query->where('utm_campaign', $this->campaign);
        })->get(['utm_campaign','lp_views', 'leads']);

        if ($stats->sum('lp_views')) {
            return ($stats->sum('leads') / $stats->sum('lp_views')) * 100;
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
