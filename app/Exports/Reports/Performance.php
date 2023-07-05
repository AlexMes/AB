<?php

namespace App\Exports\Reports;

use App\Reports\Performance\Report;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class Performance implements FromArray, WithStrictNullComparison
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
        $report         = Arr::only($this->report->toArray(), ['headers', 'rows', 'summary']);
        $report['rows'] = array_map(function ($row) {
            $row = preg_replace('/[.]/', ',', $row);

            return preg_replace('/[$|%]/', '', $row);
        }, $report['rows']->toArray());
        $report['summary'] = array_map(function ($row) {
            $row = preg_replace('/[.]/', ',', $row);

            return preg_replace('/[$|%]/', '', $row);
        }, $report['summary']);

        return $report;
    }
}
