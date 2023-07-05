<?php

namespace App\Metrics\Binom;

use App\Binom\Statistic;
use App\Metrics\Metric;
use App\Offer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

abstract class BinomMetric extends Metric
{

    /**
     * Start point of time
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $since;

    /**
     * End point of time
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $until;

    /**
     * Offers filter
     *
     * @var \Illuminate\Support\Collection
     */
    protected $offers;

    /**
     * Single offer
     *
     * @var \App\Offer
     */
    protected $offer;
    protected $users;

    /**
     * Named constructor
     *
     * @return \App\Metrics\Binom\BinomMetric
     */
    public static function make()
    {
        return new static();
    }

    /**
     * Set start date point
     *
     * @param string $date
     *
     * @return \App\Metrics\Binom\BinomMetric
     */
    public function since($date)
    {
        $this->since = Carbon::parse($date) ?? now();

        return $this;
    }

    /**
     * Set end date point
     *
     * @param string $date
     *
     * @return \App\Metrics\Binom\BinomMetric
     */
    public function until($date)
    {
        $this->until = Carbon::parse($date) ?? now();

        return $this;
    }

    /**
     * Shortcut for single date
     *
     * @param string|\DateTimeInterface $date
     *
     * @return $this
     */
    public function forDate($date)
    {
        $this->since($date)->until($date);

        return $this;
    }

    /**
     * Offers filter
     *
     * @param \Illuminate\Support\Collection|null $offers
     *
     * @return $this
     */
    public function forOffers($offers = null)
    {
        $this->offers = $offers;

        return $this;
    }

    /**
     * Limit for user
     *
     * @param null $user
     *
     * @return $this
     */
    public function forUsers($user = null)
    {
        if ($user !== null) {
            $this->users = Arr::wrap($user);
        }

        return $this;
    }

    /**
     * Get query to statistics model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function statistic(): Builder
    {
        return Statistic::forOffers($this->offers)
            ->allowedOffers()
            ->when($this->users, function (Builder $query) {
                $query->whereIn('user_id', $this->users);
            })
            ->whereBetween('date', [$this->since->toDateString(), $this->until->toDateString()]);
    }
}
