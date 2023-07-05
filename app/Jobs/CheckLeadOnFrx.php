<?php

namespace App\Jobs;

use App\CRM\TenantLeadStats;
use App\Lead;
use App\LeadOrderAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CheckLeadOnFrx implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Lead to check on FRX
     *
     * @var \App\LeadOrderAssignment
     */
    protected LeadOrderAssignment $assignment;

    /**
     * Manager, who owns assignment
     *
     * @var \App\Manager
     */
    protected $manager;

    /**
     * Create a new job instance.
     *
     * @param \App\LeadOrderAssignment $assignment
     */
    public function __construct(LeadOrderAssignment $assignment)
    {
        $this->assignment = $assignment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (optional($this->getManager())->frx_access_token !== null && $this->getManager()->tenant !== null) {
            $frxLead = $this->getFrxLeadId();
            $this->assignment->update([
                'frx_lead_id' => $frxLead['id'] ?? null,
            ]);

            $this->saveFrxLeadStats($frxLead);
        }
    }

    /**
     * Fetch lead from crm.frx
     *
     * @return array|null
     */
    protected function getFrxLeadId()
    {
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => sprintf('Bearer %s', $this->getManager()->frx_access_token),
        ])->post(sprintf('%s/api/leads/check', $this->getManager()->tenant->url), [
            'number' => $this->assignment->lead->formatted_phone,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * @return \App\Manager
     */
    protected function getManager()
    {
        if ($this->manager === null) {
            $this->manager = $this->assignment->getManager();
        }

        return $this->manager;
    }

    /**
     * @param array|null $frxLead
     *
     * @return TenantLeadStats
     */
    protected function saveFrxLeadStats(?array $frxLead)
    {
        return TenantLeadStats::create([
            'assignment_id'    => $this->assignment->id,
            'exists'           => $frxLead !== null,
            'status'           => $frxLead['status'] ?? null,
            'result'           => $frxLead['result'] ?? null,
            'closer'           => $frxLead['closer'] ?? null,
            'manager_can_view' => $frxLead['manager_can_view'] ?? null,
            'last_called_at'   => $frxLead['last_called_at'] ?? null,
            'last_viewed_at'   => $frxLead['last_viewed_at'] ?? null,
            'last_updated_at'  => $frxLead['last_updated_at'] ?? null,
        ]);
    }
}
