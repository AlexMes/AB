<?php

namespace App\Exports\Reports;

use App\Reports\Revise\Report;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class Revise implements FromArray, WithStrictNullComparison
{
    /**
     * @var Report|\App\Reports\Revise2\Report
     */
    private $report;

    public function __construct($report)
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
