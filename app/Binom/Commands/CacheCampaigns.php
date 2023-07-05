<?php

namespace App\Binom\Commands;

use App\Binom;
use App\Binom\Campaign;
use Illuminate\Support\Arr;

class CacheCampaigns extends \Illuminate\Console\Command
{
    /**
     * The name of the console command
     *
     * @var string
     */
    protected $signature = 'binom:cache:campaigns';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Cache binom campaigns';


    public function handle()
    {
        Binom::active()->each(function (Binom $binom) {
            collect($binom->getCampaigns())
                ->filter(fn ($campaign) => is_array($campaign))
                ->map(fn ($campaign)    => Arr::only($campaign, ['id', 'name','ts_id']))
                ->each(fn ($campaign)   => $this->process($binom, $campaign));
        });
    }

    /**
     * Save cached campaign to database
     *
     * @param \App\Binom $binom
     * @param $campaign
     *
     * @return void
     */
    public function process(Binom $binom, $campaign)
    {
        if ($binom->campaigns()->where('campaign_id', $campaign['id'])->exists()) {
            $this->update($binom, $campaign);
        } else {
            $this->create($binom, $campaign);
        }
    }

    /**
     * Create new campaign
     *
     * @param mixed $binom
     * @param array $campaign
     *
     * @return \App\Binom\Campaign|\Illuminate\Database\Eloquent\Model
     */
    protected function create($binom, $campaign)
    {
        return Campaign::create([
            'binom_id'    => $binom->id,
            'name'        => $campaign['name'],
            'ts_id'       => $campaign['ts_id'],
            'campaign_id' => $campaign['id'],
        ]);
    }

    /**
     * Update existing campaign
     *
     * @param array $campaign
     * @param mixed $binom
     *
     * @return bool
     */
    protected function update($binom, $campaign)
    {
        return $binom->campaigns()->where('campaign_id', $campaign['id'])->update([
            'name'  => $campaign['name'],
            'ts_id' => $campaign['ts_id'],
        ]);
    }
}
