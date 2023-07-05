<?php

namespace App\Imports;

use App\Affiliate;
use App\Jobs\ProcessLead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

class AffiliateLeadsImport implements ToCollection, WithHeadingRow, WithMapping
{
    use Importable;

    /**
     * File headings
     *
     * @var array
     */
    protected $headings;

    protected const FIRST_NAME   = 'firstname';
    protected const LAST_NAME    = 'lastname';
    protected const MIDDLE_NAME  = 'middlename';
    protected const PHONE        = 'phone';
    protected const EMAIL        = 'email';
    protected const DOMAIN       = 'domain';
    protected const CLICK_ID     = 'clickid';
    protected const CREATED_AT   = 'created_at';
    protected const AFFILIATE    = 'api_key';

    /**
     * @var \App\Affiliate
     */
    protected Affiliate $affiliate;

    /**
     * Construct Import class instance
     *
     * @param \App\Affiliate $affiliate
     * @param array          $attributes
     */
    public function __construct(Affiliate $affiliate, array $attributes)
    {
        $this->headings  = $attributes;
        $this->affiliate = $affiliate;
    }

    /**
     * @inheritDoc
     */
    public function collection(Collection $rows)
    {
        $rows->each(function ($row) {
            ProcessLead::dispatch(collect($row)->toArray());
        });
    }

    /**
     * @inheritDoc
     */
    public function map($row): array
    {
        $registrationDate = now();

        try {
            $registrationDate = Carbon::parse($row[$this->headings[self::CREATED_AT]]);
        } catch (\Throwable $exception) {
            //
        }

        return [
            self::FIRST_NAME   => $row[$this->headings[self::FIRST_NAME]] ?? null,
            self::LAST_NAME    => $row[$this->headings[self::LAST_NAME]] ?? null,
            self::MIDDLE_NAME  => $row[$this->headings[self::MIDDLE_NAME]] ?? null,
            self::PHONE        => $row[$this->headings[self::PHONE]] ?? null,
            self::DOMAIN       => $row[$this->headings[self::DOMAIN]] ?? null,
            self::EMAIL        => $row[$this->headings[self::EMAIL]] ?? null,

            self::CLICK_ID     => $row[$this->headings[self::CLICK_ID]] ?? null,
            self::CREATED_AT   => $registrationDate,
            self::AFFILIATE    => $this->affiliate->api_key
        ];
    }
}
