<?php

namespace App\Reports\CurrentRates;

use App\InitialPlan;
use App\Offer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PlanFact implements Arrayable
{
    /**
     * Date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $date;

    /**
     * Construct report
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->forDate($settings['date'] ?? null);
    }

    /**
     * Named constructor
     *
     * @param Request $request
     *
     * @return \App\Reports\CurrentRates\PlanFact
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'date' => $request->get('date'),
        ]);
    }

    /**
     * Array representation for the report
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'headers' => ['date','plan','fact','fact%'],
            'rows'    => [$this->previousDay(), $this->currentMonth()],
            'summary' => [],
        ];
    }

    /**
     * Retrieve stats for previous day
     *
     * @return array
     */
    protected function previousDay(): array
    {
        $plan = InitialPlan::query()
            ->whereDate('date', $this->date)
            ->sum('leads_amount');

        $fact = Offer::allowed()
            ->withReceivedCount($this->date)
            ->get()
            ->sum('received');

        return [
            'date'     => $this->date->toDateString(),
            'plan'     => $plan,
            'fact'     => $fact,
            'progress' => sprintf('%s %%', percentage($fact, $plan)),
        ];
    }

    /**
     * Retrieve stats for current month
     *
     * @return array
     */
    protected function currentMonth():array
    {
        $since  = now()->startOfMonth();
        $until  = now()->subDay();
        $until  = $until->isCurrentMonth() ? $until : now();

        $plan = InitialPlan::query()
            ->whereBetween('date', [$since, $until])
            ->sum('leads_amount');

        $fact = Offer::allowed()
            ->withReceivedCount(['since' => $since ,'until' => $until])
            ->get()
            ->sum('received');

        return [
            'date'     => $since->monthName,
            'plan'     => $plan,
            'fact'     => $fact,
            'progress' => sprintf('%s %%', percentage($fact, $plan)),
        ];
    }

    /**
     * Get current quick stats
     *
     * @param mixed $date
     *
     * @return \App\Reports\CurrentRates\PlanFact
     */
    public function forDate($date)
    {
        $this->date = is_null($date) ? now()->subDay() : Carbon::parse($date) ?? now()->subDay();

        return $this;
    }
}
