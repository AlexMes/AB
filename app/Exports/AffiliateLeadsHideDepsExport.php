<?php

namespace App\Exports;

use App\Affiliate;
use App\Lead;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AffiliateLeadsHideDepsExport implements FromArray, WithHeadings, WithStrictNullComparison
{
    /**
     * @var Affiliate
     */
    protected Affiliate $affiliate;

    /**
     * @var array
     */
    protected array $filters;

    public function __construct(Affiliate $affiliate, ?array $filters = null)
    {
        $this->affiliate = $affiliate;
        $this->filters   = $filters ?? [];
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return ['uuid', 'created', 'status', 'utm_source', 'utm_campaign', 'utm_content', 'utm_term', 'utm_medium'];
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->affiliate->leads()
            ->select([
                'leads.id',
                'leads.uuid',
                'leads.created_at',
                DB::Raw('COALESCE(CASE
                    WHEN
                        lead_order_assignments.status IS NULL OR
                        lead_order_assignments.status = \'Депозит\'
                    THEN
                        \'В работе\'
                    ELSE
                        lead_order_assignments.status
                    END) as status'),
                'leads.utm_source',
                'leads.utm_campaign',
                'leads.utm_content',
                'leads.utm_term',
                'leads.utm_medium',
            ])
            ->leftJoin('lead_order_assignments', 'leads.id', 'lead_order_assignments.lead_id')
            ->when(!empty($this->filters['since']), fn ($q) => $q->whereDate('leads.created_at', '>=', $this->filters['since']))
            ->when(!empty($this->filters['until']), fn ($q) => $q->whereDate('leads.created_at', '<=', $this->filters['until']))
            ->orderByDesc('leads.created_at')
            ->get()
            ->map(fn (Lead $lead) => [
                'uuid'         => $lead->uuid,
                'created_at'   => $lead->created_at,
                'status'       => !$lead->hasAssignments() || $lead->hasDeposits() ? 'В работе' : $lead->status,
                'utm_source'   => $lead->utm_source,
                'utm_campaign' => $lead->utm_campaign,
                'utm_content'  => $lead->utm_content,
                'utm_term'     => $lead->utm_term,
                'utm_medium'   => $lead->utm_medium,
            ])
            ->toArray();
    }
}
