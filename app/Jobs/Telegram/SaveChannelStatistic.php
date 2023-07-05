<?php

namespace App\Jobs\Telegram;

use App\TelegramChannelStatistic;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaveChannelStatistic implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;
    use Queueable;

    /**
     * @var array
     */
    protected array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function handle()
    {
        collect($this->attributes['channels'])
            ->map(function ($statistic) {
                return [
                    'date'        => $this->attributes['date'],
                    'channel_id'  => $statistic['id'],
                    'cost'        => $statistic['cost'] ?? 0,
                    'impressions' => $statistic['impressions'] ?? 0,
                ];
            })->each(function ($stat) {
                try {
                    TelegramChannelStatistic::create($stat);
                } catch (\Throwable $exception) {
                    \Log::debug($exception->getMessage());
                }
            });
    }
}
