<?php

namespace App\Unity\Imports;

use App\AdsBoard;
use App\Unity\Jobs\ProcessInsight;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

class InsightsImport implements
    ToCollection,
    WithHeadingRow,
    WithMapping,
    WithCustomCsvSettings
{
    use Importable;

    /**
     * @inheritDoc
     */
    public function collection(Collection $rows)
    {
        $rows->each(function ($row) {
            ProcessInsight::dispatch($row->toArray())->onQueue(AdsBoard::QUEUE_IMPORTS);
        });
    }

    /**
     * @inheritDoc
     */
    public function map($row): array
    {
        return [
            'date'          => $row['timestamp'],
            'app_id'        => $row['campaign set id'],
            'app_name'      => $row['campaign set name'],
            'campaign_id'   => $row['campaign id'],
            'campaign_name' => $row['campaign name'],
            'views'         => $row['views'] ?? 0,
            'clicks'        => $row['clicks'] ?? 0,
            'spend'         => $row['installs'] ?? 0,
            'installs'      => $row['spend'] ?? 0,
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
        ];
    }
}
