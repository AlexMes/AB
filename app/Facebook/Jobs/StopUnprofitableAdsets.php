<?php

namespace App\Facebook\Jobs;

use App\AdsBoard;
use App\Facebook\Account;
use App\Insights;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StopUnprofitableAdsets implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Account
     */
    protected $account;

    /**
     * Create a new job instance.
     *
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->shouldRun()) {
            $this->process();
        }
    }

    /**
     * Actually do work
     *
     * @return void
     */
    protected function process()
    {
        $this->adsets()
            ->filter(fn ($adset) => $this->hasHighCpl($adset) || $this->hasHighSpend($adset))
            ->each(fn ($adset)   => StopAdset::dispatch($adset)->onQueue(AdsBoard::QUEUE_STOP));
    }

    /**
     * Determine when job should even run
     *
     * @return bool
     */
    protected function shouldRun(): bool
    {
        return $this->account->stopper_enabled;
    }

    /**
     *  Defines campaign which we should to stop
     *
     * @return Insights[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    protected function adsets()
    {
        return $this->account
            ->adsets()
            ->with('campaign')
            ->active()
            ->withCurrentSpend()
            ->withCurrentCpl()
            ->withCurrentLeadsCount()
            ->get();
    }

    /**
     * Determine is adset have higher CPL than allowed
     *
     * @param \App\Facebook\AdSet $adset
     *
     * @return bool
     */
    protected function hasHighCpl($adset): bool
    {
        if ($adset->cpl !== null) {
            return $adset->cpl >= 14;
        }

        return false;
    }

    /**
     * Determines when adset spends more money than allowed
     *
     * @param \App\Facebook\AdSet $adset
     *
     * @return bool
     */
    protected function hasHighSpend($adset): bool
    {
        return ((int) $adset->leads_count == 0 && (float) $adset->spend >= 50);
    }
}
