<?php

namespace App\Reports\BuyersAccountsStats;

use Illuminate\Database\Eloquent\Collection;

class Rows
{
    /**
     * @var array
     */
    protected $rows;
    /**
     * @var Collection
     */
    protected $buyers;
    /**
     * @var Collection
     */
    protected $insights;

    /**
     * Rows constructor.
     *
     * @param $buyers
     * @param $insights
     */
    public function __construct(Collection $buyers, Collection $insights)
    {
        $this->buyers   = $buyers;
        $this->insights = $insights;
    }

    /**
     * Get report row
     *
     * @param Collection $buyers
     * @param Collection $insights
     *
     * @return Rows
     */
    public static function make(Collection $buyers, Collection $insights)
    {
        return (new self($buyers, $insights))->calculate();
    }

    /**
     * @return Rows
     */
    protected function calculate()
    {
        $this->rows = $this->buyers->map(function ($user) {
            $total = $user->accounts->count();
            $spend = $this->getSpend($total > 0, $user->accounts);

            return [
                Fields::NAME               => $user->name,
                Fields::ACTIVE             => $user->accounts->where('account_status', 1)->count(),
                Fields::SPEND              => $spend,
                Fields::AVG_SPEND          => $this->getAvgSpend($total > 0, $user->accounts, $spend),
                Fields::DISABLED           => $user->accounts->where('account_status', 2)->count(),
                Fields::UNSETTLED          => $user->accounts->where('account_status', 3)->count(),
                Fields::PENDING_REVIEW     => $user->accounts->where('account_status', 7)->count(),
                Fields::PENDING_SETTLEMENT => $user->accounts->where('account_status', 8)->count(),
                Fields::GRADE_PERIOD       => $user->accounts->where('account_status', 9)->count(),
                Fields::OTHERS             => $user->accounts->whereNotIn('account_status', [1,2,3,7,8,9])->count(),
                Fields::TOTAL              => $total,
                Fields::TOTAL_COST         => $this->getTotalCost($total > 0, $user->accounts),
                Fields::BALANCE            => $this->getBalance($total > 0, $user->accounts),
            ];
        })
            ->reject(function ($row) {
                return $row['totalCost'] < 1;
            });

        return $this;
    }

    /**
     * @param $hasAccounts
     * @param $accounts
     *
     * @return false|float|int
     */
    protected function getTotalCost($hasAccounts, $accounts)
    {
        return $hasAccounts ?
            round($accounts->sum('amount_spent') / 100, 2)
            : 0;
    }

    /**
     * @param $hasAccounts
     * @param $accounts
     *
     * @return false|float|int
     */
    protected function getBalance($hasAccounts, $accounts)
    {
        return $hasAccounts ?
            round($accounts->sum(function ($account) {
                return (float) $account->balance;
            }), 2)
            : 0;
    }

    /**
     * @param $hasAccounts
     * @param $accounts
     * @param $spend
     *
     * @return false|float|int
     */
    protected function getAvgSpend($hasAccounts, $accounts, $spend)
    {
        return $hasAccounts ?
            round($spend / $accounts->count(), 2)
            : 0;
    }

    /**
     * Calculate actual costs
     *
     * @param $hasAccounts
     * @param $accounts
     *
     * @return mixed
     */
    protected function getSpend($hasAccounts, $accounts)
    {
        return $hasAccounts ?
            round($this->insights
                ->whereIn('account_id', $accounts->pluck('account_id')->toArray())
                ->sum('spend'), 2)
            : 0;
    }

    /**
     * Get calculated rows
     *
     * @return mixed
     */
    public function values()
    {
        return $this->rows;
    }

    /**
     * Pretty formatted rows
     *
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    public function metric()
    {
        return collect($this->rows)->map(function ($row) {
            return [
                Fields::NAME               => $row['name'],
                Fields::ACTIVE             => $row['active'],
                Fields::SPEND              => sprintf('%s $', $row['spend']),
                Fields::AVG_SPEND          => sprintf('%s $', $row['avgSpend']),
                Fields::DISABLED           => $row['disabled'],
                Fields::UNSETTLED          => $row['unsettled'],
                Fields::PENDING_REVIEW     => $row['pendingReview'],
                Fields::PENDING_SETTLEMENT => $row['pendingSettlement'],
                Fields::GRADE_PERIOD       => $row['gradePeriod'],
                Fields::OTHERS             => $row['others'],
                Fields::TOTAL              => $row['total'],
                Fields::TOTAL_COST         => sprintf('%s $', $row['totalCost']),
                Fields::BALANCE            => sprintf('%s $', $row['balance']),
            ];
        })
            ->values();
    }
}
