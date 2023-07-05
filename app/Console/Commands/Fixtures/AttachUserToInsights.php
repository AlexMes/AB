<?php

namespace App\Console\Commands\Fixtures;

use App\Facebook\Campaign;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AttachUserToInsights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:attach-users-to-insights';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Walk thorough all facebook campaigns, and attach related stats to them';
    /**
     * @var \App\User[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $tags;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $campaigns = Campaign::query();

        $progress = $this->output->createProgressBar($campaigns->count());

        /** @var \App\Facebook\Campaign $campaign */
        foreach ($campaigns->cursor() as $campaign) {
            /** @var \App\User $buyer */
            $buyer = $this->getBuyer($campaign);
            if ($buyer !== null) {
                $campaign->cachedInsights()->update([
                    'user_id' => $buyer->id,
                ]);
            }
            $progress->advance();
        }

        $progress->finish();
    }

    /**
     * Find buyer using binom tag
     *
     * @param \App\Facebook\Campaign $campaign
     *
     * @return \App\User|null
     */
    protected function getBuyer(Campaign $campaign)
    {
        $buyer = $this->getBuyerUsingTag($campaign);
        if ($buyer !== null) {
            return $buyer;
        }

        return $this->getBuyerFromProfile($campaign);
    }

    /**
     * Resolve buyer id from tag in campaign
     *
     * @param \App\Facebook\Campaign $campaign
     *
     * @return mixed
     */
    protected function getBuyerUsingTag(Campaign $campaign)
    {
        return $this->tags()
            ->filter(fn ($user) => Str::contains(Str::lower($campaign->name), Str::lower($user->binomTag)))
            ->first();
    }

    /**
     * @return \App\User[]|\Illuminate\Database\Eloquent\Collection
     */
    protected function tags()
    {
        if (! $this->tags) {
            $this->tags = User::get(['id','binomTag'])->sortBy(fn ($user) => strlen($user->binomTag));
        }

        return $this->tags;
    }

    /**
     * Get buyer from the profile
     *
     * @param \App\Facebook\Campaign $campaign
     *
     * @return \App\User|null
     */
    protected function getBuyerFromProfile(Campaign $campaign)
    {
        return optional(optional($campaign->account)->profile)->user;
    }
}
