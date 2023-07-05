<?php

namespace App\Jobs;

use App\DistributionRule;
use App\Lead;
use App\LeadAssigner\LeadAssigner;
use App\Trail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RunLeftOversAssignment implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $date;
    protected $amount;
    protected $offers;
    protected $offices;
    protected $leads;
    protected $simulateAutologin;
    protected $deliverUntil;
    protected $retry;
    protected $replaceAuthId;
    protected $deliverSince;
    protected $sort;
    protected $destinationId;

    /**
     * Create a new job instance.
     *
     * @param null       $date
     * @param null       $amount
     * @param array      $offers
     * @param array      $offices
     * @param mixed      $leads
     * @param mixed      $simulateAutologin
     * @param null|mixed $deliverUntil
     * @param bool       $retry
     * @param null       $replaceAuthId
     * @param null|mixed $deliverSince
     * @param string     $sort
     * @param null|mixed $destinationId
     */
    public function __construct(
        $date = null,
        $amount = null,
        $offers = [],
        $offices = [],
        $leads = [],
        $simulateAutologin = false,
        $deliverUntil = null,
        $retry = false,
        $replaceAuthId = null,
        $deliverSince = null,
        $sort = null,
        $destinationId = null
    ) {
        $this->date              = $date;
        $this->amount            = $amount;
        $this->offers            = $offers;
        $this->offices           = $offices;
        $this->leads             = $leads;
        $this->simulateAutologin = $simulateAutologin;
        $this->deliverUntil      = $deliverUntil;
        $this->retry             = $retry;
        $this->replaceAuthId     = $replaceAuthId;
        $this->deliverSince      = $deliverSince ? Carbon::parse($deliverSince) : null;
        $this->sort              = $sort ?? null;
        $this->destinationId     = $destinationId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(Trail::class)->add('Amount of leads specified:' . $this->amount);
        $leadQuery = Lead::visible()->leftovers()
            ->when($this->amount !== null && $this->amount > 0, fn (Builder $query) => $query->limit($this->amount * 2))
            ->when($this->date, fn ($query) => $query->whereBetween(DB::raw('created_at::date'), $this->date))
            ->notEmptyWhereIn('offer_id', $this->offers)
            ->notEmptyWhereIn('leads.id', $this->leads)
            ->when($this->destinationId, fn ($query) => $query->withoutDeliveryFailed($this->destinationId))
            ->orderBy('leads.created_at', $this->sort ?? 'asc');

        $leadsCount = $this->amount !== null && $this->amount > 0
            ? min((int)$this->amount, $leadQuery->count())
            : $leadQuery->count();

        $deliverAt = null;
        if ($this->deliverUntil !== null) {
            $deliverAt   = ($this->deliverSince ? $this->deliverSince->copy() : now())->floorSeconds(60)->addMinute();
            $minutesLeft = $deliverAt->diffInMinutes($this->deliverUntil);
            $step        = $leadsCount > 0 ? $minutesLeft / $leadsCount : 0;
            $addMinCnt   = 0;
            \Log::channel('smooth-lo')->debug(sprintf(
                'Request started: %s, deliver since:%s, deliver until:%s, deliver_at:%s, minLeft:%s, leads:%s, step:%s',
                now()->toDateTimeString(),
                optional($this->deliverSince)->toDateTimeString('minute'),
                $this->deliverUntil,
                $deliverAt->toDateTimeString(),
                $minutesLeft,
                $leadsCount,
                $step
            ));
        }

        $duplicates = collect();
        $count      = 0;

        foreach ($leadQuery->cursor() as $lead) {
            if ($count >= $leadsCount) {
                break;
            }

            if ($duplicates->contains($lead->phone)) {
                continue;
            }
            $duplicates->push($lead->phone);
            $count++;

            app(Trail::class)->add(sprintf('Assigning leftover lead %s. Geo is %s %s', $lead->id, optional($lead->ipAddress)->country_code, optional($lead->lookup)->country));
            if ($this->offices) {
                $rules = DistributionRule::whereIn('office_id', $this->offices)->where('offer_id', $lead->offer_id)->get(['id','geo','allowed'])
                    ->map(fn (DistributionRule $rule) => sprintf('**%s** -> __%s__', $rule->geo, $rule->allowance))->implode(' / ');
                if ($rules) {
                    app(Trail::class)->add('Distribution rules ' . $rules);
                }
            }
            app(Trail::class)->add('Authenticated user is ' . optional(auth()->user())->name ?? 'CLI');

            LeadAssigner::dispatchNow($lead, null, $this->offices, null, null, $this->simulateAutologin, $deliverAt, $this->retry, false, $this->replaceAuthId, null, $this->deliverSince, $this->sort);
            if ($deliverAt !== null) {
                $addMinCnt += $step;
                if ($addMinCnt >= 1) {
                    $deliverAt->addMinutes((int)$addMinCnt);
                    $addMinCnt = $addMinCnt - (int)$addMinCnt;
                }
                \Log::channel('smooth-lo')->debug(sprintf(
                    'Next iteration. Deliver at:%s, addMin:%s',
                    $deliverAt->toDateTimeString(),
                    $addMinCnt
                ));
            } else {
                sleep(1);
            }
        }
        if ($deliverAt !== null) {
            \Log::channel('smooth-lo')->debug(sprintf('Request finished: %s', now()->toDateTimeString()));
        }
    }
}
