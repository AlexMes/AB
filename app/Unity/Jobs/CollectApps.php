<?php

namespace App\Unity\Jobs;

use App\Unity\Exceptions\UnityException;
use App\UnityApp;
use App\UnityOrganization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CollectApps implements ShouldQueue
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
     * CollectApps constructor.
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

        $this->retrieve()->each(fn ($app) => $this->save($app));
    }

    /**
     * @return Collection
     */
    protected function retrieve(): Collection
    {
        $limit = 1000;
        $page  = 1;
        try {
            $result = collect();
            do {
                $apps = $this->organization->initUnityApp()
                    ->fetchApps($this->organization->organization_core_id, [
                        'limit'  => $limit,
                        'offset' => ($page - 1) * $limit
                    ]);

                $result->push(...$apps['results']);
            } while ($apps['total'] > $limit * $page++);

            return $result;
        } catch (UnityException $exception) {
            $this->organization->addIssue($exception);

            return collect();
        } catch (\Throwable $exception) {
            $this->organization->addIssue($exception);

            return collect();
        }
    }

    /**
     * Save app information into database
     *
     * @param array $app
     *
     * @return void
     */
    protected function save(array $app)
    {
        UnityApp::updateOrCreate(
            ['id' => $app['id']],
            [
                'organization_id' => $this->organization->id,
                'name'            => $app['name'],
                'store'           => $app['store'],
                'store_id'        => $app['storeId'],
                'adomain'         => $app['adomain'],
            ],
        );
    }
}
