<?php

namespace App\Reports\CurrentRates;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ThreeDayConversion implements Arrayable
{
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
     * @return \App\Reports\CurrentRates\ThreeDayConversion
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
            'headers' => ['date','same day','next day','in 3 days'],
            'rows'    => [$this->threeDaysAgo(), $this->currentMonth()],
            'summary' => [],
        ];
    }

    /**
     * Retrieve stats for 3 days ago
     *
     * @return array
     */
    protected function threeDaysAgo(): array
    {
        $results = (new \App\Reports\Conversion\Report())
            ->since($this->date->copy()->subDays(2))
            ->until($this->date->copy()->subDays(2))
            ->toArray()['summary'];

        return [
            'date'  => $this->date->copy()->subDays(2)->toDateString(),
            'same'  => $results['same'],
            'next'  => $results['next'],
            'third' => $results['third']
        ];
    }


    /**
     * Retrieve stats for current month
     *
     * @return array
     */
    protected function currentMonth(): array
    {
        $since  = now()->startOfMonth();
        $until  = now()->subDay();
        $until  = $until->isCurrentMonth() ? $until : now();

        $results = (new \App\Reports\Conversion\Report())
            ->since($since)
            ->until($until)
            ->toArray()['summary'];

        return [
            'date'  => $since->monthName,
            'same'  => $results['same'],
            'next'  => $results['next'],
            'third' => $results['third']
        ];
    }

    /**
     * Get current quick stats
     *
     * @param mixed $date
     *
     * @return \App\Reports\CurrentRates\ThreeDayConversion
     */
    public function forDate($date)
    {
        $this->date = is_null($date) ? now()->subDay() : Carbon::parse($date) ?? now()->subDay();

        return $this;
    }
}
