<?php


namespace App\Facebook\Jobs;

use App\Facebook\Profile;
use App\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncAccountGroup implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;
    use InteractsWithQueue;

    /**
     * @var Profile
     */
    protected $profile;
    /**
     * @var int|null
     */
    protected $newGroup;
    /**
     * @var int|null
     */
    protected $oldGroup;

    /**
     * SyncAccountGroup constructor.
     *
     * @param Profile  $profile
     * @param int|null $newGroup
     * @param int|null $oldGroup
     */
    public function __construct(Profile $profile, $newGroup, $oldGroup)
    {
        $this->profile  = $profile;
        $this->newGroup = $newGroup;
        $this->oldGroup = $oldGroup;
    }

    public function handle()
    {
        if ($this->isDetach()) {
            $this->detach();

            return;
        }

        $this->sync();
    }

    /**
     * Determines when we should detach group from accounts
     *
     * @return bool
     */
    protected function isDetach()
    {
        return is_null($this->newGroup);
    }

    /**
     * Detached group from account when user remove group in profile
     *
     * @return void
     */
    protected function detach()
    {
        $this->profile
            ->accounts()
            ->where('group_id', '=', $this->oldGroup)
            ->update(['group_id' => null]);
    }

    /**
     * Sync group in accounts when user set up group or change it
     *
     * @return void
     */
    protected function sync()
    {
        $this->profile
            ->accounts()
            ->where(function ($query) {
                $query->where('group_id', '=', $this->oldGroup)
                    ->orWhereNull('group_id');
            })
            ->update(['group_id' => $this->newGroup]);
    }
}
