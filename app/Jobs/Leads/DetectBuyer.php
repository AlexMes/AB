<?php

namespace App\Jobs\Leads;

use App\Lead;
use App\ManualAccount;
use App\ManualCampaign;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DetectBuyer implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\Lead
     */
    protected $lead;

    /**
     * Create a new job instance.
     *
     * @param \App\Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($user = $this->getBuyerUsingTag()) {
            $this->lead->recordAs(Lead::BUYER_DETECTED)->update([
                'user_id' => $user->id,
            ]);
        } elseif ($this->lead->account_id !== null) {
            $this->lead->recordAs(Lead::BUYER_DETECTED)->update([
                'user_id' => ManualAccount::where('account_id', $this->lead->account_id)->value('user_id') ?? $this->lead->account->profile->user_id,
            ]);
        } elseif ($user = optional(ManualCampaign::whereName($this->lead->utm_source)->first())->user) {
            $this->lead->recordAs(Lead::BUYER_DETECTED)->update([
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Resolve buyer id from tag in utm source
     *
     * @return User|null
     */
    protected function getBuyerUsingTag()
    {
        if (preg_match('~-[a-zA-Z0-9_]+-~', $this->lead->utm_source, $tag)) {
            return User::query()
                ->whereNotIn('role', [User::GAMBLE_ADMIN, User::GAMBLER])
                ->where('binomTag', 'ilike', $tag[0])->first();
        }

        return null;
    }
}
