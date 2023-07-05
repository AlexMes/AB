<?php

namespace App\Reports\AccountsBanned;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class Summary implements Arrayable
{
    /**
     * Insights collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $insights;

    /**
     * Accounts collection
     *
     * @var \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $accounts;

    /**
     * @var string
     */
    protected $groupBy;

    /**
     * Determines when we should filter strictly
     *
     * @var bool
     */
    protected $isStrict = false;

    /**
     * DailyReportSummary constructor.
     *
     * @param \Illuminate\Support\Collection $insights
     * @param Collection                     $accounts
     * @param string                         $groupBy
     * @param bool                           $isStrict
     */
    public function __construct(Collection $insights, Collection $accounts, string $groupBy, $isStrict = false)
    {
        $this->insights       = $insights;
        $this->accounts       = $accounts;
        $this->groupBy        = $groupBy;
        $this->isStrict       = $isStrict;
    }

    /**
     * DailyReportSummary constructor.
     *
     * @param \Illuminate\Support\Collection $insights
     * @param Collection                     $accounts
     * @param string                         $groupBy
     * @param bool                           $isStrict
     *
     * @return \App\Reports\AccountsBanned\Summary
     */
    public static function build(Collection $insights, Collection $accounts, string $groupBy, $isStrict = false)
    {
        return new self($insights, $accounts, $groupBy, $isStrict);
    }

    /**
     * Get array representation of summary
     *
     * @return array
     */
    public function toArray()
    {
        switch ($this->groupBy) {
            case 'spend':
                return $this->groupBySpend();
            case 'group':
                return $this->groupByGroups();
            default:
                return $this->groupByAccounts();
        }
    }

    /**
     * @return array
     */
    public function groupBySpend()
    {
        $lifetime = $this->getLifetime();

        return [
            Fields::NAME      => 'Итого',
            Fields::RK_PERCENT=> '',
            Fields::RK_COUNT  => ($rk = $this->isStrict ? $this->insights->count() : $this->accounts->count()),
            Fields::SPEND     => sprintf("\$ %s", round($spend = $this->insights->sum('spend'), 2)),
            Fields::LIFETIME  => $this->getLifetimeMetric(round($rk ? $lifetime / $rk : 0, 2)),
            Fields::AVG_SPEND => sprintf("\$ %s", round($rk ? $spend / $rk : 0, 2)),
        ];
    }

    /**
     * @return array
     */
    public function groupByAccounts()
    {
        $lifetime = $this->getLifetime();

        return [
            Fields::NAME      => 'Итого',
            Fields::BUYER     => null,
            Fields::RK_COUNT  => ($rk = $this->isStrict ? $this->insights->count() : $this->accounts->count()),
            Fields::SPEND     => sprintf("\$ %s", round($spend = $this->insights->sum('spend'), 2)),
            Fields::LIFETIME  => $this->getLifetimeMetric(round($rk ? $lifetime / $rk : 0, 2)),
            Fields::AVG_SPEND => sprintf("\$ %s", round($rk ? $spend / $rk : 0, 2)),
        ];
    }

    /**
     * @return array
     */
    public function groupByGroups()
    {
        $lifetime = $this->getLifetime();

        return [
            Fields::NAME      => 'Итого',
            Fields::RK_COUNT  => ($rk = $this->isStrict ? $this->insights->count() : $this->accounts->count()),
            Fields::SPEND     => sprintf("\$ %s", round($spend = $this->insights->sum('spend'), 2)),
            Fields::LIFETIME  => $this->getLifetimeMetric(round($rk ? $lifetime / $rk : 0, 2)),
            Fields::AVG_SPEND => sprintf("\$ %s", round($rk ? $spend / $rk : 0, 2)),
        ];
    }

    /**
     * @return mixed
     */
    protected function getLifetime()
    {
        return collect(
            $this->accounts
                ->when($this->isStrict, function ($collection) {
                    return $collection->whereIn('account_id', $this->insights->pluck('account_id')->toArray());
                })
                ->map(fn ($account) => $account->lifetime)
        )->sum();
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
}
