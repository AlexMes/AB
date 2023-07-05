<?php

namespace App\Facebook\Commands;

use App\AdsBoard;
use App\Facebook\Ad;
use Illuminate\Console\Command;

class FlushComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fb:comments:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush comments on fan pages';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->pendingAds()
            ->each(fn ($ad) => \App\Facebook\Jobs\FlushComments::dispatch($ad)->onQueue(AdsBoard::QUEUE_CLEANING));
    }

    /**
     * @return \App\Facebook\Ad[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    protected function pendingAds()
    {
        return Ad::active()
            ->select(['id', 'account_id', 'page_id'])
            ->with([
                'profile' => fn ($query) => $query->select(['facebook_profiles.id', 'token'])->withIssuesCount(),
            ])
            ->whereHas('cachedInsights', function ($builder) {
                $builder->where('date', now()->toDateString());
            })->get()
            ->reject(fn ($ad) => $ad->profile->pending_issues > 0);
    }
}
