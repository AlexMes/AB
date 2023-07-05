<?php

namespace App\Exports\Reports;

use App\Reports\LeadStats\Report;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class LeadStats implements FromArray, WithStrictNullComparison
{
    /**
     * @var Report
     */
    private $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return Arr::only($this->report->toArray(), ['headers', 'rows', 'summary']);
    }
}
