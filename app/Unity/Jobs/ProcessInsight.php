<?php

namespace App\Unity\Jobs;

use App\Unity\Pipes\EnsureAppExists;
use App\Unity\Pipes\EnsureCampaignExists;
use App\Unity\Pipes\ValidateDate;
use App\UnityInsight;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ProcessInsight implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var array
     */
    protected array $attributes;

    /**
     * Array of processing classes
     *
     * @var array
     */
    protected $pipes = [
        EnsureAppExists::class,
        EnsureCampaignExists::class,
        ValidateDate::class,
    ];

    /**
     * Create a new job instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = app(Pipeline::class)
            ->send($this->attributes)
            ->through($this->pipes)
            ->thenReturn();

        $this->saveInsight($result);
    }

    /**
     * @param array|null $attributes
     *
     * @throws \Throwable
     *
     * @return UnityInsight|\Illuminate\Database\Eloquent\Model|null
     */
    protected function saveInsight(?array $attributes)
    {
        if ($attributes === null) {
            return null;
        }

        return UnityInsight::updateOrCreate(
            [
                'date'        => Carbon::parse($attributes['date']),
                'app_id'      => $attributes['app_id'],
                'campaign_id' => $attributes['campaign_id'],
            ],
            Arr::only($attributes, ['views', 'clicks', 'spend', 'installs']),
        );
    }
}
