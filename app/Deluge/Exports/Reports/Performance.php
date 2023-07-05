<?php

namespace App\Deluge\Exports\Reports;

use App\Deluge\Reports\Performance\Report;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromArray;

class Performance implements FromArray
{
    /**
     * @var array
     */
    protected Report $report;

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
        }, $report['rows']);
        $report['summary'] = array_map(function ($row) {
            $row = preg_replace('/[.]/', ',', $row);

            return preg_replace('/[$|%]/', '', $row);
        }, $report['summary']);

        return $report;
    }
}
