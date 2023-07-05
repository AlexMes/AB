<?php

namespace App\VK\Jobs;

use App\Lead;
use App\VK\Exceptions\VKException;
use App\VK\Models\Profile;
use App\VK\Pipes\CheckLeadForDuplicates;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Log;

/**
 * Class CollectLeads
 *
 * @package App\VK\Jobs
 */
class CollectLeads implements ShouldQueue
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
     * @var array|string[]
     */
    protected array $pipes = [
        CheckLeadForDuplicates::class,
    ];

    /**
     * CollectLeads constructor.
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

        $this->retrieve()->each(fn ($lead) => $this->save($lead));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function retrieve()
    {
        try {
            return $this->profile
                ->fresh()
                ->forms()
                ->active()
                ->get()
                ->flatMap(function ($form) {
                    $result = $this->profile->initVKApp()->fetchLeads([
                        'group_id' => $form->vk_group_id,
                        'form_id'  => $form->vk_id,
                    ])['leads'];

                    return collect($result)->map(function ($item) use ($form) {
                        $questions = collect($form->questions)->keyBy('key');
                        foreach ($item['answers'] as $answer) {
                            $question = $questions->get($answer['key']);
                            // default
                            if (!isset($question['label'])) {
                                $item[$answer['key']] = $answer['answer']['value'] ?? null;
                            } else {
                                $item['custom_answers'][$answer['key']] = [
                                    'label' => $question['label'],
                                    'value' => $answer['answer']['value'] ?? collect($answer['answer'])->pluck('value')->join(','),
                                ];
                            }
                        }

                        return $item;
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
     *
     * @param array $lead
     *
     * @return void
     */
    protected function save(array $lead)
    {
        app(Pipeline::class)
            ->send(new Lead([
                'user_id'    => $this->profile->user_id,
                'firstname'  => $lead['first_name'] ?? null,
                'lastname'   => $lead['last_name'] ?? null,
                'middlename' => $lead['patronymic_name'] ?? null,
                'email'      => $lead['email'] ?? null,
                'phone'      => $lead['phone_number'] ?? null,
                'poll'       => array_merge(
                    $lead['custom_answers'],
                    isset($lead['age']) ? ['age' => ['label' => 'Возраст','value' => $lead['age']]] : [],
                    isset($lead['birthday']) ? ['birthday' => ['label' => 'День рождения','value' => $lead['birthday']]] : [],
                    isset($lead['location']) ? ['location' => ['label' => 'Город и страна','value' => $lead['location']]] : [],
                )
            ]))
            ->through($this->pipes)
            ->then(fn (Lead $leadModel) => $leadModel->save());
    }
}
