<?php

namespace App\Unity\Jobs;

use App\Unity\Exceptions\UnityException;
use App\Unity\Imports\InsightsImport;
use App\UnityOrganization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CollectInsights implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 3;

    /**
     * @var UnityOrganization
     */
    protected UnityOrganization $organization;

    /**
     * @var \Illuminate\Support\Carbon
     */
    protected $since;

    /**
     * @var \Illuminate\Support\Carbon
     */
    protected $until;

    /**
     * CollectInsights constructor.
     *
     * @param UnityOrganization $organization
     * @param null              $since
     * @param null              $until
     */
    public function __construct(
        UnityOrganization $organization,
        $since = null,
        $until = null
    ) {
        $this->since        = ($since ? Carbon::parse($since) : now())->startOfDay();
        $this->until        = ($until ? Carbon::parse($until) : now())->endOfDay();
        $this->organization = $organization;
        $this->organization->refresh();

        if ($this->since->greaterThan($this->until)) {
            $this->until = $this->since->copy();
        }
    }

    /**
     * Process a job
     *
     * @return void
     */
    public function handle()
    {
        // The rate limit is 1 request every 3 seconds per IP address.
        if (!Cache::lock('collect-insights-lock', 3)->get()) {
            $this->release(3);

            return;
        }

        try {
            $result = $this->organization->initUnityApp()->fetchInsights($this->organization->organization_id, [
                'start'   => $this->since->toIso8601String(),
                'end'     => $this->until->toIso8601String(),
                'scale'   => 'day',
                'splitBy' => 'campaignSet,campaign',
                'fields'  => 'timestamp,campaignSet,campaign,views,clicks,installs,spend',
                //filters
                /*'campaignSets' => 'id1,id2',
                'campaigns'    => 'id11,id22',*/
            ]);

            $path = sprintf('unity/stats_%s_%s.csv', time(), Str::random(32));
            Storage::disk('tmp')->put($path, $result);

            /*Excel::import(new InsightsImport(), 'unity\stats_1667228460_yYzkFJKtZFp5srUqxu5qn7tWY2oJxKma.csv', 'tmp');*/
            Excel::import(new InsightsImport(), $path, 'tmp');
        } catch (UnityException $exception) {
            $this->organization->addIssue($exception);
        } catch (\Throwable $exception) {
            $this->organization->addIssue($exception);
        }
    }
}
