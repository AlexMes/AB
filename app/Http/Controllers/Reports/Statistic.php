<?php

namespace App\Http\Controllers\Reports;

use App\Facebook\Collectors\InsightCollector;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Class Statistic
 *
 * Base statistics for the facebook entities
 * MUST use fresh, realtime data from the API
 *
 * @package App\Http\Controllers
 */
class Statistic extends Controller
{
    /**
     * Insights collecting "service"
     *
     * @var \App\Facebook\Collectors\InsightCollector
     */
    protected $collector;

    /**
     * AdsInsights constructor.
     *
     * @param \App\Facebook\Collectors\InsightCollector $collector
     */
    public function __construct(InsightCollector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * Collect insights report
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Facebook\Collectors\InsightCollector
     */
    public function __invoke(Request $request)
    {
        return $this->collector
            ->forAccounts($request->get('accounts'))
            ->forPeriod(
                json_decode($request->get('period'), true)
            )
            ->atLevel($request->get('mode'))
            ->forStatus($request->get('statuses'));
    }
}
