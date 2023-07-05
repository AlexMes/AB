<?php

namespace App\Unity\Jobs;

use App\Unity\Exceptions\UnityException;
use App\UnityCampaign;
use App\UnityOrganization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CollectCampaigns implements ShouldQueue
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
     * @var bool
     */
    protected bool $force = true;

    /**
     * CollectCampaigns constructor.
     *
     * @param UnityOrganization $organization
     * @param bool              $force
     */
    public function __construct(
        UnityOrganization $organization,
        bool $force = true
    ) {
        $this->force        = $force;
        $this->organization = $organization;
        $this->organization->refresh();
    }

    /**
     * Process a job
     *
     * @return void
     */
    public function handle()
    {
        if ($this->organization->hasIssues() && $this->force === false) {
            return;
        }

        $this->retrieve()->each(fn ($campaign) => $this->save($campaign));
    }

    /**
     * @return Collection
     */
    protected function retrieve(): Collection
    {
        try {
            return $this->organization
                ->apps()
                ->pluck('id')
                ->flatMap(function ($appId) {
                    $result = $this->organization->initUnityApp()
                        ->fetchCampaigns($this->organization->organization_core_id, $appId);

                    return collect($result['results'])->map(function ($campaign) use ($appId) {
                        $campaign['app_id'] = $campaign['app_id'] ?? $appId;

                        return $campaign;
                    });
                });
        } catch (UnityException $exception) {
            $this->organization->addIssue($exception);

            return collect();
        } catch (\Throwable $exception) {
            $this->organization->addIssue($exception);

            return collect();
        }
    }

    /**
     * Save campaign information into database
     *
     * @param array $campaign
     *
     * @return void
     */
    protected function save(array $campaign)
    {
        UnityCampaign::updateOrCreate(
            ['id' => $campaign['id']],
            [
                'app_id'  => $campaign['app_id'],
                'name'    => $campaign['name'],
                'goal'    => $campaign['goal'],
                'enabled' => $campaign['enabled'],
            ],
        );
    }
}
