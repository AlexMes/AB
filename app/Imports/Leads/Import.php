<?php

namespace App\Imports\Leads;

use App\AdsBoard;
use App\Jobs\Leads\UpdateLeadData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

class Import implements ToCollection, WithHeadingRow, WithMapping, ShouldQueue, WithChunkReading
{
    use Importable;

    /**
     * File headings
     *
     * @var array
     */
    protected $headings;

    protected const FIRST_NAME          = 'firstname';
    protected const LAST_NAME           = 'lastname';
    protected const PHONE               = 'phone';
    protected const EXTERNAL_ID         = 'external_id';
    protected const UTM_CAMPAIGN        = 'utm_campaign';
    protected const UTM_SOURCE          = 'utm_source';
    protected const UTM_MEDIUM          = 'utm_medium';
    protected const UTM_CONTENT         = 'utm_content';
    protected const UTM_TERM            = 'utm_term';
    protected const DOMAIN              = 'domain';
    protected const CLICK_ID            = 'clickid';
    protected const STATUS              = 'status';
    protected const RESPONSIBLE         = 'responsible';
    protected const COMMENT             = 'comment';
    protected const OFFICE_ID           = 'office_id';
    protected const DEPARTMENT_ID       = 'department_id';
    protected const CALLED_AT           = 'called_at';

    /**
     * Construct Import class instance
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->headings = $attributes;
    }

    /**
     * @inheritDoc
     */
    public function collection(Collection $rows)
    {
        $rows->reject(function ($row) {
            return is_null($row['external_id']);
        })
            ->each(function ($row) {
                UpdateLeadData::dispatch(collect($row)->toArray())->onQueue(AdsBoard::QUEUE_IMPORTS);
            });
    }

    /**
     * @inheritDoc
     */
    public function map($row): array
    {
        return [
            self::EXTERNAL_ID         => $row[$this->headings[self::EXTERNAL_ID]] ?? null,
            self::STATUS              => $row[$this->headings[self::STATUS]] ?? null,
            self::RESPONSIBLE         => $row[$this->headings[self::RESPONSIBLE]] ?? null,
            self::COMMENT             => $row[$this->headings[self::COMMENT]] ?? null,
            self::OFFICE_ID           => $row[$this->headings[self::OFFICE_ID]] ?? null,
            self::DEPARTMENT_ID       => $row[$this->headings[self::DEPARTMENT_ID]] ?? null,
            self::CALLED_AT           => $row[$this->headings[self::CALLED_AT]] ?? null,
        ];
    }

    /**
     * @inheritDoc
     */
    public function chunkSize(): int
    {
        return 50;
    }
}
