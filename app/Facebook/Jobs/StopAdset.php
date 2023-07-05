<?php

namespace App\Facebook\Jobs;

use App\Bot\Telegram;
use App\Facebook\AdSet;
use App\StoppedAdset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Throwable;

class StopAdset implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;
    use InteractsWithQueue;

    /**
     * @var \App\Facebook\AdSet
     */
    protected $adset;

    /**
     * StopAdset constructor.
     *
     * @param \App\Facebook\AdSet $adset
     *
     * @return void
     */
    public function __construct($adset)
    {
        $this->adset = $adset;
    }

    /**
     * Process the job
     *
     * @param \App\Bot\Telegram $telegram
     *
     * @return void
     */
    public function handle(Telegram $telegram)
    {
        $adset = AdSet::withCurrentCpl()->withCurrentSpend()->find($this->adset->id);

        try {
            $this->adset->stop();

            $telegram->say(
                sprintf(
                    "Adset %s stopped. Spend: %s, CPL: %s",
                    $adset->id,
                    $adset->spend ?? 0,
                    $adset->cpl ?? 0
                )
            )
                ->to('929700298')
                ->send();
            StoppedAdset::create([
                'adset_id'   => $adset->id,
                'account_id' => $adset->account_id,
                'spend'      => $adset->spend ?? 0,
                'cpl'        => $adset->cpl ?? 0,
            ]);
        } catch (Throwable $exception) {
//            Cache::lock(sprintf("stopper-errors-%s", $this->adset->id), now()->addHours(20))->get(fn () =>
//            $telegram->say(sprintf(
//                "Stopping adset %s failed. \n %s",
//                $adset->id,
//                $exception->getMessage(),
//            ))
//                ->to('929700298')
//                ->send());
        }
    }
}
