<?php

namespace App\Exports\Reports;

use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class DefaultExport implements FromArray, WithStrictNullComparison
{
    /**
     * @var Report
     */
    protected $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function array(): array
    {
        return Arr::only($this->report->toArray(), ['headers', 'rows', 'summary']);
    }
}
