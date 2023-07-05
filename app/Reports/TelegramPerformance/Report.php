<?php

namespace App\Reports\TelegramPerformance;

use App\Queries\PerformanceDeposits;
use App\Queries\TelegramPerformanceStatistics;
use App\Queries\TelegramPerformanceTraffic;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class Report implements Arrayable, Responsable
{
    public const LEVEL_CAMPAIGN = 'campaign';
    public const LEVELS         = [
        self::LEVEL_CAMPAIGN,
    ];

    /**
     * Start date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $since;

    /**
     * End date for report
     *
     * @var \Illuminate\Support\Carbon
     */
    protected $until;

    /**
     * Collection of results row
     *
     * @var \Illuminate\Support\Collection
     */
    protected $rows;

    /**
     * Level of reporting
     *
     * @var string|null
     */
    protected $level;

    /**
     * @var int
     */
    protected $subject;

    /**
     * UTM Campaign filter
     *
     * @var array
     */
    protected $utm_campaign;

    public function __construct(array $settings = [])
    {
        $this->since($settings['since'] ?? now())
            ->until($settings['until'] ?? now())
            ->atLevel($settings['level'] ?? self::LEVEL_CAMPAIGN)
            ->forCampaign($settings['utmCampaign'] ?? null)
            ->forSubject($settings['subject'] ?? null);
    }

    public static function fromRequest(Request $request)
    {
        return new static([
            'since'        => $request->get('since'),
            'until'        => $request->get('until'),
            'level'        => $request->get('level'),
            'utmCampaign'  => $request->get('campaign'),
            'subject'      => $request->get('subject'),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $statistics = $this->statistics();
        $deposits   = $this->deposits();
        $traffic    = $this->traffic();

        return [
            'headers'  => Headers::forLevel($this->level),
            'rows'     => Rows::build($statistics, $deposits, $this->level, $traffic)->toArray(),
            'summary'  => Summary::build($statistics, $deposits, $this->level, $traffic)->toArray(),
            'period'   => [
                'since' => $this->since->startOfDay()->toDateTimeString(),
                'until' => $this->until->endOfDay()->toDateTimeString()
            ],
        ];
    }

    /**
     * Load statistics
     *
     * @return \App\TelegramChannelStatistic[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function statistics()
    {
        return TelegramPerformanceStatistics::fetch()
            ->forPeriod($this->since, $this->until)
            ->forCampaign($this->utm_campaign)
            ->forSubject($this->subject)
            ->atLevel($this->level)
            ->get();
    }

    /**
     * Fetch deposits
     *
     * @return \Illuminate\Support\Collection
     */
    protected function deposits()
    {
        return PerformanceDeposits::fetch()
            ->forPeriod($this->since, $this->until)
            ->forCampaign($this->utm_campaign)
            ->atLevel($this->level)
            ->forSubject($this->subject)
            ->get();
    }

    /**
     * Cached binom's statistic
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function traffic()
    {
        return TelegramPerformanceTraffic::fetch()
            ->forPeriod($this->since, $this->until)
            ->forCampaign($this->utm_campaign)
            ->forSubject($this->subject)
            ->get();
    }

    /**
     * Get response representation of the report
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), 200);
    }

    /**
     * Set start of report time range
     *
     * @param null $since
     *
     * @return \App\Reports\TelegramPerformance\Report
     */
    protected function since($since = null)
    {
        if (is_null($since)) {
            $this->since = now();

            return $this;
        }

        $this->since = Carbon::parse($since);

        return $this;
    }

    /**
     * Set end of report time range
     *
     * @param null $until
     *
     * @return \App\Reports\TelegramPerformance\Report
     */
    protected function until($until = null)
    {
        if (is_null($until)) {
            $this->until = now();

            return $this;
        }

        $this->until = Carbon::parse($until);

        return $this;
    }

    /**
     * Set level of report
     *
     * @param string|null $level
     *
     * @return \App\Reports\TelegramPerformance\Report
     */
    protected function atLevel($level)
    {
        $this->level = in_array($level, self::LEVELS) ? $level : self::LEVEL_CAMPAIGN;

        return $this;
    }

    /**
     * Filter by offers
     *
     * @param string $campaign
     *
     * @return \App\Reports\TelegramPerformance\Report
     */
    protected function forCampaign($campaign)
    {
        $this->utm_campaign = $campaign;

        return $this;
    }

    /**
     * Filters by telegram subject
     *
     * @param $subject
     *
     * @return $this
     */
    protected function forSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }
}
