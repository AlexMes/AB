<?php

namespace App\Metrics;

use App\Metrics\Binom\BinomMetric;
use Illuminate\Database\Eloquent\Builder;

class PrelandingConversion extends BinomMetric
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
     * @return \App\Metrics\PrelandingConversion
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
        })->get(['lp_clicks', 'lp_views']);

        if ($stats->sum('lp_views')) {
            return ($stats->sum('lp_clicks') / $stats->sum('lp_views')) * 100;
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
