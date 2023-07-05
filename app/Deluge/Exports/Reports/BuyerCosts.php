<?php

namespace App\Deluge\Exports\Reports;

use App\Deluge\Reports\BuyerCosts\Report;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BuyerCosts implements WithHeadings, FromArray
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
     * @return string[]
     */
    public function headings(): array
    {
        return ['user', 'account_id', 'date', 'sum'];
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $data = $this->report->toArray();

        $result = array_map(function ($row) {
            return [
                'user'       => $row['buyer'],
                'account_id' => "'" . $row['account_id'] ?? '',
                'date'       => $row['date'],
                'sum'        => $row['cost'],
            ];
        }, $data['rows']);
        $result[] = [
            'user'       => $data['summary']['date'],
            'account_id' => '',
            'date'       => '',
            'sum'        => $data['summary']['cost'],
        ];

        return $result;
    }
}
