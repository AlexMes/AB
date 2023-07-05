<?php


namespace App\Queries;

use App\Reports\TelegramPerformance\Report;
use App\TelegramChannel;
use App\TelegramChannelStatistic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TelegramPerformanceStatistics
{

    /**
     * Telegram Channel Statistics query builder
     *
     * @var \App\TelegramChannelStatistic
     */
    protected $statistics;

    /**
     * Array of fields required for query
     *
     * @var array
     */
    protected static $fields = [
        'date', 'channel_id', 'cost', 'impressions',
    ];

    /**
     * Telegram Performance Statistics Query constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->statistics = TelegramChannelStatistic::query()
            ->unless(auth()->user()->isAdmin(), function ($builder) {
                return $builder->whereHas('channel', function (Builder $query) {
                    return $query->whereExists(function (\Illuminate\Database\Query\Builder $q) {
                        return $q->select(DB::raw('1'))
                            ->from('leads')
                            ->whereColumn('telegram_channels.name', 'leads.utm_campaign')
                            ->whereIn('leads.offer_id', auth()->user()->allowedOffers->pluck('id')->values());
                    });
                });
            });
    }

    /**
     * Set dates range
     *
     * @param \Carbon\Carbon $since
     * @param \Carbon\Carbon $until
     *
     * @return \App\Queries\TelegramPerformanceStatistics
     */
    public function forPeriod($since, $until)
    {
        $this->statistics->whereBetween('date', [$since->toDateString(), $until->toDateString()]);

        return $this;
    }

    /**
     * Filter report for specific campaign
     *
     * @param string $campaign
     *
     * @return \App\Queries\TelegramPerformanceStatistics
     */
    public function forCampaign($campaign = null)
    {
        if ($campaign === null) {
            return $this;
        }

        $this->statistics->whereHas('channel', function ($query) use ($campaign) {
            /*$query->where('name', 'like', "%campaign-{$campaign}");*/
            $query->where('name', 'like', "%{$campaign}");
        });

        return $this;
    }

    /**
     * Filter report for specific subject
     *
     * @param string $subject
     *
     * @return \App\Queries\TelegramPerformanceStatistics
     */
    public function forSubject($subject = null)
    {
        if ($subject === null) {
            return $this;
        }

        $this->statistics->whereHas('channel', function ($query) use ($subject) {
            $query->where('subject_id', '=', $subject);
        });

        return $this;
    }

    /**
     * Set level of report
     *
     * @param string|null $level
     *
     * @return \App\Queries\TelegramPerformanceStatistics
     */
    public function atLevel($level = Report::LEVEL_CAMPAIGN)
    {
        if ($level === Report::LEVEL_CAMPAIGN) {
            $this->statistics->addSelect([
                'name' => TelegramChannel::select('name')
                    ->whereColumn('id', 'telegram_channel_statistics.channel_id')
                    ->limit(1)
            ]);
        }

        return $this;
    }

    /**
     * Perform query and return results
     *
     * @return \App\TelegramChannelStatistic[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get()
    {
        return $this->statistics->get(self::$fields);
    }


    /**
     * Named constructor
     *
     * @return \App\Queries\TelegramPerformanceStatistics
     */
    public static function fetch()
    {
        return new self();
    }
}
