<?php

namespace App\VK\Jobs;

use App\VK\Exceptions\VKException;
use App\VK\Models\Group;
use App\VK\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class CollectProfilePages
 *
 * @package App\VK\Jobs
 */
class CollectGroups implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public $tries = 3;

    /**
     * @var Profile
     */
    protected Profile $profile;

    /**
     * @var bool
     */
    protected bool $force = true;

    /**
     * CollectGroups constructor.
     *
     * @param Profile $profile
     * @param bool    $force
     */
    public function __construct(
        Profile $profile,
        bool $force = true
    ) {
        $this->force   = $force;
        $this->profile = $profile;
        $this->profile->refresh();
    }

    /**
     * Process a job
     *
     * @return void
     */
    public function handle()
    {
        if ($this->profile->hasErrors() && $this->force === false) {
            return;
        }

        $this->retrieve()->each(fn ($group) => $this->save($group));
    }

    /**
     * Load profile groups from the VK API
     *
     * @return Collection
     */
    protected function retrieve(): Collection
    {
        try {
            return collect($this->profile->initVKApp()->fetchGroups(['filter' => 'admin', 'extended' => true])['items']);
        } catch (VKException $vkException) {
            return collect();
        } catch (\Throwable $exception) {
            Log::warning(
                sprintf(
                    "Server error happened when vk syncing [%d] %s",
                    $this->profile->id,
                    $this->profile->name
                )
            );

            return collect();
        }
    }

    /**
     * Save page information into database
     *
     * @param array $group
     *
     * @return void
     */
    protected function save(array $group)
    {
        // profile should not be overwritten !
        $model = Group::firstOrNew(
            ['vk_id' => $group['id']],
            ['profile_id' => $this->profile->id, 'name' => $group['name']],
        );
        $model->name = $group['name'];

        $model->save();
    }
}
