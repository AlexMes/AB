<?php

namespace App\Deluge\Jobs;

use App\Deluge\Pipes\EnsureAccountExists;
use App\Deluge\Pipes\ValidateDate;
use App\ManualBundle;
use App\ManualCampaign;
use App\ManualInsight;
use App\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
        EnsureAccountExists::class,
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
     * @return ManualInsight|\Illuminate\Database\Eloquent\Model|null
     */
    protected function saveInsight(?array $attributes)
    {
        if ($attributes === null) {
            return null;
        }

        $this->resolveCampaign($attributes);

        return ManualInsight::updateOrCreate(
            [
                'date'        => Carbon::parse($attributes['date_end']),
                'account_id'  => $attributes['account_id'],
                'campaign_id' => $attributes['campaign_id'],
            ],
            Arr::except($attributes, ['campaign_name', 'date_start', 'date_end']),
        );
    }

    /**
     * @param array $attributes
     *
     * @throws \Throwable
     *
     * @return ManualCampaign|ManualCampaign[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    protected function resolveCampaign(array $attributes)
    {
        $campaign = ManualCampaign::find($attributes['campaign_id']);
        if ($campaign === null) {
            ManualCampaign::create([
                'id'         => $attributes['campaign_id'],
                'name'       => $attributes['campaign_name'],
                'account_id' => $attributes['account_id'],
                'bundle_id'  => $this->resolveBundle($attributes)->id,
            ]);
        }

        return $campaign;
    }

    /**
     * @param array $attributes
     *
     * @throws \Throwable
     *
     * @return ManualBundle|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    protected function resolveBundle(array $attributes)
    {
        $utmKey = trim(Str::afterLast($attributes['campaign_name'], 'campaign-'));

        $bundle = ManualBundle::whereName($utmKey)->first();
        if ($bundle === null) {
            $offer = Offer::find(1);
            throw_if($offer === null, new \Exception('Could not resolve bundle.'));

            $bundle = ManualBundle::create([
                'name'     => $utmKey,
                'offer_id' => $offer->id,
            ]);
        }

        return $bundle;
    }
}
