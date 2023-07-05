<?php

namespace App\Metrics\Telegram;

use Illuminate\Database\Eloquent\Collection;

class TelegramMetric
{

    /**
     * Binom statistics
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $traffic;

    /**
     * Telegram Channel Statistics collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $statistics;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * TelegramMetric constructor.
     *
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $traffic
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $statistics
     */
    public function __construct(Collection $traffic, Collection $statistics)
    {
        $this->traffic    = $traffic;
        $this->statistics = $statistics;
    }

    /**
     * Gets sum cost from Telegram statistics
     *
     * @return float|int
     */
    public function cost()
    {
        if (!isset($this->data['cost'])) {
            $this->data['cost'] = optional($this->statistics)->sum('cost') ?? 0;
        }

        return $this->data['cost'];
    }

    /**
     * Gets sum impressions from Telegram statistics
     *
     * @return int
     */
    public function impressions()
    {
        if (!isset($this->data['impressions'])) {
            $this->data['impressions'] = optional($this->statistics)->sum('impressions') ?? 0;
        }

        return $this->data['impressions'];
    }

    /**
     * Unique clicks
     *
     * @return int
     */
    public function clicks()
    {
        if (!isset($this->data['clicks'])) {
            $this->data['clicks'] = optional($this->traffic)->sum('unique_clicks') ?? 0;
        }

        return $this->data['clicks'];
    }

    /**
     * @return int
     */
    public function lpViews()
    {
        if (!isset($this->data['lp_views'])) {
            $this->data['lp_views'] = optional($this->traffic)->sum('lp_views') ?? 0;
        }

        return $this->data['lp_views'];
    }

    /**
     * @return int
     */
    public function lpClicks()
    {
        if (!isset($this->data['lp_clicks'])) {
            $this->data['lp_clicks'] = optional($this->traffic)->sum('lp_clicks') ?? 0;
        }

        return $this->data['lp_clicks'];
    }

    /**
     * @return int
     */
    public function leads()
    {
        if (!isset($this->data['leads'])) {
            $this->data['leads'] = optional($this->traffic)->sum('leads') ?? 0;
        }

        return $this->data['leads'];
    }

    /**
     * all clicks
     *
     * @return int
     */
    public function bclicks()
    {
        if (!isset($this->data['bclicks'])) {
            $this->data['bclicks'] = optional($this->traffic)->sum('clicks') ?? 0;
        }

        return $this->data['bclicks'];
    }


    /**
     * @return string
     */
    public function getCpm()
    {
        $value = 0;
        if ($this->impressions()) {
            $value = round($this->cost() / ($this->impressions() / 1000), 2);
        }

        return sprintf("$ %s", $value);
    }

    /**
     * @return string
     */
    public function getCpc()
    {
        $value = 0;
        if ($this->clicks()) {
            $value = round($this->cost() / $this->clicks(), 2);
        }

        return sprintf("$ %s", $value);
    }

    /**
     * @return string
     */
    public function getCtr()
    {
        $value = 0;
        if ($this->impressions()) {
            $value = round($this->clicks() / $this->impressions() * 100, 2);
        }

        return sprintf("%s %%", $value);
    }

    /**
     * Get CRPL (LP CTR)
     *
     * @return float|int
     */
    public function getLpCtr()
    {
        $value = 0;
        if ($this->lpViews()) {
            $value = round($this->lpClicks() / $this->lpViews() * 100, 2);
        }

        return sprintf("%s %%", $value);
    }

    /**
     * Get CRLP (offer CR)
     *
     * @return int
     */
    public function getOfferCr()
    {
        $value = 0;
        if ($this->lpClicks()) {
            $value = round($this->leads() / $this->lpClicks() * 100, 2);
        }

        return sprintf("%s %%", $value);
    }

    /**
     * Get CRLP (offer CR)
     *
     * @param string $name
     *
     * @return int
     */
    public function getCr()
    {
        $value = 0;
        if ($this->bclicks()) {
            $value = round($this->leads() / $this->bclicks() * 100, 2);
        }

        return sprintf("%s %%", $value);
    }
}
