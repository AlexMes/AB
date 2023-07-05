<?php

namespace App\Jobs;

use App\Lead;
use App\LeadAssigner\LeadAssigner;
use App\LeadOrderAssignment;
use App\LeadOrderRoute;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ReplaceAssignmentLead implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var LeadOrderAssignment
     */
    protected LeadOrderAssignment $assignment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LeadOrderAssignment $assignment)
    {
        $this->assignment = $assignment->loadMissing(['route.order']);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $route = $this->assignment->route;

        // pause route to skip live assignments during this job.
        $routeStatus = $route->status;
        $route->update(['status' => LeadOrderRoute::STATUS_PAUSED]);

        $this->assignment->remove();

        $caucasian   = $route->order->office->disallow_caucasian;
        $currentAuth = auth()->user();
        auth()->setUser(User::find($this->assignment->replace_auth_id));
        $lead = Lead::visible()
            ->leftovers()
            ->whereOfferId($route->offer_id)
            ->withoutDeliveryFailed($this->assignment->destination_id)
            ->when($caucasian, function (Builder $query) {
                return $query->whereDoesntHave('markers')->whereName('caucasian');
            })
            ->when($route->order->deny_live, fn ($q) => $q->whereDate('created_at', '<', now()->toDateString()))
            ->orderBy('leads.created_at', $this->assignment->smooth_sort ?? 'asc')
            ->first();
        if ($currentAuth !== null) {
            auth()->setUser($currentAuth);
        } else {
            auth()->logout();
        }

        \Log::channel('smooth-lo')->debug(sprintf(
            'Replacing lead on assignment. Ass: %s, route: %s, new lead: %s(%s)',
            $this->assignment->id,
            $this->assignment->route_id,
            optional($lead)->id,
            optional($lead)->fullname,
        ));

        $cacheKey2  = sprintf('smooth-lo-replace-cnt-at-route-%s-%s', $route->id, now()->toTimeString('minute'));
        $cntOnRoute = (int)Cache::get($cacheKey2, 0);
        Cache::set($cacheKey2, $cntOnRoute + 1, Carbon::SECONDS_PER_MINUTE);

        if ($lead !== null) {
            LeadAssigner::dispatchNow(
                $lead,
                null,
                null,
                null,
                null,
                $this->assignment->simulate_autologin,
                $this->assignment->deliver_at !== null
                    ? now()->floorSeconds(60)->addMinutes(2 + min(10, $cntOnRoute))
                    : null,
                false,
                false,
                $this->assignment->replace_auth_id,
                $route,
                null,
                $this->assignment->smooth_sort
            );
        }

        $route->update(['status' => $routeStatus]);
    }
}
