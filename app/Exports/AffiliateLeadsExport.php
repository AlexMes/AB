<?php

namespace App\Exports;

use App\Affiliate;
use App\Lead;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AffiliateLeadsExport implements FromArray, WithHeadings, WithStrictNullComparison
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
        return ['name', 'phone', 'email', 'valid', 'created', 'status'];
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->affiliate->leads()
            ->select([
                'leads.firstname',
                'leads.lastname',
                'leads.middlename',
                'leads.phone',
                'leads.email',
                'leads.valid',
                'leads.created_at',
                'lead_order_assignments.status',
            ])
            ->leftJoin('lead_order_assignments', 'leads.id', 'lead_order_assignments.lead_id')
            ->when(!empty($this->filters['since']), fn ($q) => $q->whereDate('leads.created_at', '>=', $this->filters['since']))
            ->when(!empty($this->filters['until']), fn ($q) => $q->whereDate('leads.created_at', '<=', $this->filters['until']))
            ->orderByDesc('leads.created_at')
            ->get()
            ->map(fn (Lead $lead) => [
                'name'       => $lead->fullname,
                'phone'      => $lead->phone,
                'email'      => $lead->email,
                'valid'      => $lead->valid,
                'created_at' => $lead->created_at,
                'status'     => $lead->status,
            ])
            ->toArray();
    }
}
