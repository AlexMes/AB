<?php

namespace App\Console\Commands\Fixtures;

use App\Binom;
use App\Lead;
use Illuminate\Console\Command;

class PullMissingClicks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixtures:pull-missing-clicks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Walk through leads without click info, and try to pull it';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = Lead::whereDoesntHave('click')->latest()->whereNull('affiliate_id')->whereNotNull('clickid');

        $binoms = Binom::active()->get();

        foreach ($leads->cursor() as $lead) {
            $hero = $binoms->skipUntil(function (Binom $binom) use ($lead) {
                try {
                    $this->info(sprintf("Approaching %s with click %s", $binom->name, $lead->clickid));
                    $binom->getClick($lead->clickid);
                    $this->info(sprintf("Got the click from %s", $binom->name));

                    return true;
                } catch (Binom\Exceptions\BinomReponseException $exception) {
                    $this->warn(sprintf("Failed to pull click %s from %s", $lead->clickid, $binom->name));

                    return false;
                }
            })->first();

            if ($hero !== null) {
                $this->info('Got the click');
                $clickData = array_merge(['binom_id' => $hero->id], $hero->getClick($lead->clickid));
                $click     = $lead->click()->create(array_merge([
                    'clickid'    => $lead->clickid,
                    'conversion' => $clickData['conversion'] ?? false
                ], $clickData));

                $lead->addEvent(
                    Lead::CLICK_INFO_PULLED,
                    ['id' => $click->id, 'clickid' => $lead->clickid]
                );
            }
        }

        return 0;
    }
}
