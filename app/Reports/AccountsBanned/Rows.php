<?php

namespace App\Reports\AccountsBanned;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Rows implements Arrayable
{
    /**
     * Insights collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $insights;

    /**
     * Insights collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $accounts;

    /**
     * @var null
     */
    protected $offers;

    protected $lvl;

    /**
     * Determines when we should filter strictly
     *
     * @var bool
     */
    protected $isStrict = false;

    /**
     * DailyReportSummary constructor.
     *
     * @param Collection $insights
     * @param $accounts
     * @param mixed $lvl
     * @param bool  $isStrict
     */
    public function __construct($insights, $accounts, $lvl = 'account', $isStrict = false)
    {
        $this->insights       = $insights;
        $this->accounts       = $accounts;
        $this->lvl            = $lvl;
        $this->isStrict       = $isStrict;
    }

    /**
     * Named constructor
     *
     * @param $insights
     * @param $accounts
     * @param mixed $lvl
     * @param $isStrict
     *
     * @return \App\Reports\AccountsBanned\Rows
     */
    public static function build($insights, $accounts, $lvl, $isStrict)
    {
        return new static($insights, $accounts, $lvl, $isStrict);
    }

    /**
     * Get array representation of single row
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function toArray()
    {
        switch ($this->lvl) {
            case "spend":
                return $this->accounts
                    ->groupBy(function ($account, $key) {
                        return $this->defineSpendRange(
                            $this->insights->where('account_id', $account->account_id)->sum('spend')
                        );
                    })
                    ->sortBy(fn ($group, $name) => array_search($name, array_keys(Report::SPEND_RANGES), true))
                    ->map(function (Collection $group, $name) {
                        $group = $group->when($this->isStrict, function ($collection) {
                            return $collection->whereIn('account_id', $this->insights->pluck('account_id')->toArray());
                        });
                        $insights = $this->insights->whereIn(
                            'account_id',
                            $group->unique('account_id')
                                ->pluck('account_id')
                                ->toArray()
                        );
                        $lifetime = $group->sum('lifetime');

                        $rk = $group->count();

                        return [
                            Fields::NAME            => $name === "" ? 'None' : $name,
                            Fields::RK_PERCENT      => sprintf("%s %%", round($rk / $this->accounts->count() * 100, 2)),
                            Fields::RK_COUNT        => $rk,
                            Fields::SPEND           => sprintf("\$ %s", round($spend = $insights->sum('spend'), 2)),
                            Fields::LIFETIME        => $this->getLifetimeMetric(round($rk ? $lifetime / $rk : 0, 2)),
                            Fields::AVG_SPEND       => sprintf("\$ %s", round($rk ? $spend / $rk : 0, 2)),
                        ];
                    });
            case "group":
                return $this->accounts
                    ->groupBy('group_name')
                    ->map(function ($group, $name) {
                        $group = $group->when($this->isStrict, function ($collection) {
                            return $collection->whereIn('account_id', $this->insights->pluck('account_id')->toArray());
                        });
                        $insights = $this->insights->whereIn('account_id', $group->unique('account_id')
                            ->pluck('account_id')
                            ->toArray());
                        $lifetime = collect($group->map(fn ($account) => $account->lifetime))->sum();

                        return [
                            Fields::NAME            => $name == "" ? 'None' : $name,
                            Fields::RK_COUNT        => $rk = $group->count(),
                            Fields::SPEND           => sprintf("\$ %s", round($spend = $insights->sum('spend'), 2)),
                            Fields::LIFETIME        => $this->getLifetimeMetric(round($rk ? $lifetime / $rk : 0, 2)),
                            Fields::AVG_SPEND       => sprintf("\$ %s", round($rk ? $spend / $rk : 0, 2)),
                        ];
                    });
            default:
                return $this->accounts
                    ->when($this->isStrict, function ($collection) {
                        return $collection->whereIn('account_id', $this->insights->pluck('account_id')->toArray());
                    })
                    ->map(function ($account) {
                        $insights = $this->insights->where('account_id', $account->account_id)->first();

                        return [
                            Fields::NAME            => $account->name,
                            Fields::BUYER           => optional($account->user)->name,
                            Fields::RK_COUNT        => 1,
                            Fields::SPEND           => sprintf("\$ %s", round($spend = optional($insights)->spend, 2)),
                            Fields::LIFETIME        => $this->getLifetimeMetric($account->lifetime),
                            Fields::AVG_SPEND       => sprintf("\$ %s", round($spend, 2)),
                        ];
                    });
        }
    }

    /**
     * @param $lifetimeHours
     *
     * @return string
     */
    protected function getLifetimeMetric($lifetimeHours)
    {
        $days = $lifetimeHours / 24;

        if ($days > 1) {
            return sprintf("%s %s", round($days, 2), ($days === 1 ? "день" : "дней"));
        }

        return sprintf("%s часов", $lifetimeHours);
    }

    protected function defineSpendRange($spend)
    {
        foreach (Report::SPEND_RANGES as $title => $range) {
            $values = explode('-', $range);

            if (!isset($values[1])) {
                if ($spend == $values[0]) {
                    return $title;
                }
            } elseif ($spend >= $values[0] && $spend < $values[1]) {
                return $title;
            }
        }

        return 'none';
    }
}
