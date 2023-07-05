<?php

namespace App\Facebook\Reports;

use App\Facebook\Contracts\Insightful;
use App\Facebook\Objects\Insight;

class AdvertisementStats
{
    /**
     * Entity, which has insights on Facebook
     *
     * @var \App\Facebook\Contracts\Insightful
     */
    protected $insightful;

    /**
     * Array of params for Facebook API
     *
     * @var array
     */
    protected $params = ['date_preset' => 'this_month'];

    /**
     * AdvertisementStats constructor.
     *
     * @param \App\Facebook\Contracts\Insightful $insightful
     */
    public function __construct(Insightful $insightful)
    {
        $this->insightful = $insightful;
    }

    /**
     * Named constructor
     *
     * @param \App\Facebook\Contracts\Insightful $insightful
     *
     * @return \App\Facebook\Reports\AdvertisementStats
     */
    public static function make(Insightful $insightful)
    {
        return new static($insightful);
    }

    /**
     * Final call in chain. Returns array of results
     *
     * @return array|void
     */
    public function getResults()
    {
        return array_merge([
            'id'          => $this->insightful->getId(),
            'name'        => $this->insightful->getName(),
            'status'      => $this->insightful->getStatus(),
            'account'     => $this->insightful->getFBAccount(),
            'budget'      => $this->insightful->getBudget(),
            'leads'       => $this->getLeadsCount(),
        ], $this->getInsights());
    }

    /**
     * Set Facebook filters and settings
     *
     * @param array $params
     *
     * @return \App\Facebook\Reports\AdvertisementStats
     */
    public function usingParams($params = [])
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Load insights from the Facebook API
     *
     * @return array
     */
    protected function getInsights()
    {
        $insight =  $this->insightful->insights($this->params)->first();

        if (is_null($insight)) {
            return                 [
                'reach'           => 0,
                'frequency'       => 0,
                'cpr'             => 0,
                'ctr'             => 0,
                'spend'           => 0,
                'ends'            => 0,
                'impressions'     => 0,
                'cpm'             => 0,
                'clicks'          => 0,
                'cpc'             => 0,
            ];
        }

        return [
            'reach'       => optional($insight)->reach ?? 0,
            'frequency'   => round(optional($insight)->frequency ?? 0, 2),
            'cpr'         => $this->getCpr($insight),
            'ctr'         => round(optional($insight)->ctr ?? 0, 2),
            'spend'       => optional($insight)->spend ?? 0,
            'ends'        => optional($insight)->ends ?? 0,
            'impressions' => optional($insight)->impressions ?? 0,
            'cpm'         => round(optional($insight)->cpm ?? 0, 2),
            'clicks'      => optional($insight)->clicks ?? 0,
            'cpc'         => round(optional($insight)->cpc ?? 0, 2),
        ];
    }

    /**
     * Get cost per result
     *
     * @param \App\Facebook\Objects\Insight $insight
     *
     * @return float
     */
    public function getCpr(Insight $insight)
    {
        return round(optional($insight)->spend / $this->getLeadsCount() ?? 1, 2);
    }

    /**
     * Get count of leads, came here in specified period
     *
     * @return int
     */
    public function getLeadsCount()
    {
        return 1;
    }
}
