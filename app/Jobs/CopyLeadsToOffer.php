<?php

namespace App\Jobs;

use App\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CopyLeadsToOffer implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var array
     */
    protected array $params;

    /**
     * Create a new job instance.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Lead::withoutCopies()
            ->whereOfferId($this->params['offer_from'])
            ->whereBetween(DB::raw('leads.created_at::date'), [$this->params['since'], $this->params['until']])
            ->when(
                $this->params['country'],
                fn ($query, $country) => $query->whereHas(
                    'ipAddress',
                    fn ($q) => $q->where('country_name', $country)
                )
            )
            ->limit(max(1, $this->params['amount'] ?? 1))
            ->get()
            ->each(fn (Lead $lead) => $lead->createCopyToOffer($this->params));
    }
}
