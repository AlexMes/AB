<?php

namespace App\Reports\BuyersAccountsStats;

class Summary
{
    /**
     * Get report summary
     *
     * @param mixed $rows
     *
     * @return array
     */
    public static function make($rows)
    {
        return [
            Fields::NAME               => 'ИТОГО',
            Fields::ACTIVE             => $rows->sum('active'),
            Fields::SPEND              => sprintf('%s $', round($rows->sum('spend'), 2)),
            Fields::AVG_SPEND          => sprintf('%s $', self::getAvgSpend($rows)),
            Fields::DISABLED           => $rows->sum('disabled'),
            Fields::UNSETTLED          => $rows->sum('unsettled'),
            Fields::PENDING_REVIEW     => $rows->sum('pendingReview'),
            Fields::PENDING_SETTLEMENT => $rows->sum('pendingSettlement'),
            Fields::GRADE_PERIOD       => $rows->sum('gradePeriod'),
            Fields::OTHERS             => $rows->sum('others'),
            Fields::TOTAL              => $rows->sum('total'),
            Fields::TOTAL_COST         => sprintf('%s $', round($rows->sum('totalCost'), 2)),
            Fields::BALANCE            => sprintf('%s $', round($rows->sum('balance'), 2)),
        ];
    }

    /**
     * just wrapper
     *
     * @param $rows
     *
     * @return false|float|int
     */
    protected static function getAvgSpend($rows)
    {
        return $rows->sum('total') > 0 ?
            round($rows->sum('spend') / $rows->sum('total'), 2)
            : 0;
    }
}
