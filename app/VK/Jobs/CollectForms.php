<?php

namespace App\VK\Jobs;

use App\VK\Exceptions\VKException;
use App\VK\Models\Form;
use App\VK\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

/**
 * Class CollectForms
 *
 * @package App\VK\Jobs
 */
class CollectForms implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    /**
     * @var int
     */
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
     * CollectForms constructor.
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

        $this->retrieve()->each(fn ($form) => $this->save($form));
    }

    /**
     * Load profile pages from the VK API
     *
     * @return \Illuminate\Support\Collection
     */
    protected function retrieve()
    {
        try {
            return $this->profile
                ->fresh()
                ->groups()
                ->pluck('vk_id')
                ->flatMap(function ($vkGroupId) {
                    $result = $this->profile->initVKApp()->fetchLeadForms([
                        'group_id' => $vkGroupId,
                    ]);

                    return collect($result)->map(function ($form) use ($vkGroupId) {
                        $form['group_id'] = $form['group_id'] ?: $vkGroupId;

                        return $form;
                    })->toArray();
                });
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
     * Cache vk form
     *
     * @param array $form
     *
     * @return void
     */
    protected function save(array $form)
    {
        Form::query()->updateOrCreate(
            ['vk_id' => $form['form_id'], 'vk_group_id' => $form['group_id']],
            [
                'name'      => $form['name'],
                'questions' => $form['questions'],
                'raw_data'  => $form,
                'is_active' => $form['active'],
            ]
        );
    }
}
