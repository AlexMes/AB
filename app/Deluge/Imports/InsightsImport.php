<?php

namespace App\Deluge\Imports;

use App\AdsBoard;
use App\Deluge\Jobs\ProcessInsight;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

    protected const HEADERS = [
        'ru' => [
            'date_start'    => 'Дата начала отчетности',
            'date_end'      => 'Дата окончания отчетности',
            'account_id'    => 'Идентификатор аккаунта',
            'campaign_id'   => 'Идентификатор кампании',
            'campaign_name' => 'Название кампании',
            'impressions'   => 'Показы',
            'clicks'        => 'Уникальные клики по ссылке',
            'spend'         => 'Сумма затрат (USD)',
            'leads_cnt'     => 'Результаты',
        ],
        'ua' => [
            'date_start'    => 'Початок звітності',
            'date_end'      => 'Завершення звітності',
            'account_id'    => 'Ідентифікаційний код облікового запису',
            'campaign_id'   => 'Ідентифікаційний номер кампанії',
            'campaign_name' => 'Назва кампанії',
            'impressions'   => 'Покази',
            'clicks'        => 'Unique Link Clicks',
            'spend'         => 'Сума витрат (USD)',
            'leads_cnt'     => 'Результати',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function collection(Collection $rows)
    {
        Validator::make($rows->toArray(), $this->rules())->validate();

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
            'date_start'        => $this->resolveValue($row, 'date_start') ?? null,
            'date_end'          => $this->resolveValue($row, 'date_end') ?? null,
            'account_id'        => (string)$this->resolveValue($row, 'account_id') ?? null,
            'campaign_id'       => (string)$this->resolveValue($row, 'campaign_id') ?? null,
            'campaign_name'     => $this->resolveValue($row, 'campaign_name') ?? '',
            'impressions'       => $this->resolveValue($row, 'impressions') ?? 0,
            'clicks'            => $this->resolveValue($row, 'clicks') ?? 0,
            'spend'             => $this->resolveValue($row, 'spend') ?? 0,
            'leads_cnt'         => $this->resolveValue($row, 'leads_cnt') ?? 0,
        ];
    }

    public function rules(): array
    {
        return [
            '*.date_start' => ['required', 'date'],
            '*.date_end'   => ['required', 'date', 'same:*.date_start'],
            '*.account_id' => Rule::exists('manual_accounts', 'account_id'),
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
        ];
    }

    /**
     * @param array  $row
     * @param string $headerKey
     *
     * @return mixed|string|null
     */
    protected function resolveValue(array $row, string $headerKey)
    {
        $value = null;

        foreach (self::HEADERS as $code => $headers) {
            if (isset($row[$headers[$headerKey]])) {
                $value = $row[$headers[$headerKey]];
                break;
            }
        }

        return $value;
    }
}
