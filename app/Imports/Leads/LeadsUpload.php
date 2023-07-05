<?php

namespace App\Imports\Leads;

use App\AdsBoard;
use App\Affiliate;
use App\Jobs\Imports\ProcessLead;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadsUpload implements ToCollection, WithHeadingRow, WithMapping
{
    use Importable;

    /**
     * @var
     */
    public $validator;

    /**
     * @var null
     */
    public $offer_id = null;

    /**
     * @var int|null
     */
    public ?int $affiliate_id = null;

    /**
     * @var bool|null
     */
    public ?bool $ignore_doubles = false;

    /**
     * @var bool
     */
    public $offerIdRequired = false;

    protected const HEADERS = [
        'api_key'       => 'api_key',
        'firstname'     => 'firstname',
        'lastname'      => 'lastname',
        'middlename'    => 'middlename',
        'phone'         => 'phone',
        'ip'            => 'ip',
        'email'         => 'email',
        'form_type'     => 'form_type',
        'utm_campaign'  => 'utm_campaign',
        'utm_source'    => 'utm_source',
        'utm_medium'    => 'utm_medium',
        'utm_content'   => 'utm_content',
        'utm_term'      => 'utm_termm',
        'domain'        => 'domain',
        'clickid'       => 'clickid',
    ];

    /**
     * @inheritDoc
     */
    public function collection(Collection $rows)
    {
        $this->validateApiKey($rows->map(fn ($row) => $row['api_key']));
        $this->validator = Validator::make($rows->toArray(), $this->rules(), $this->customValidationMessages())->validate();

        $rows->each(function ($row) {
            ProcessLead::dispatch(
                array_merge(collect($row)->toArray(), [
                    'offer_id'  => $this->offer_id,
                ]),
                $this->ignore_doubles
            )
                ->onQueue(AdsBoard::QUEUE_IMPORTS);
        });
    }

    /**
     * @inheritDoc
     */
    public function map($row): array
    {
        $mapped = [
            'api_key'       => $this->resolveValue($row, 'api_key') ?? null,
            'firstname'     => $this->resolveValue($row, 'firstname') ?? null,
            'lastname'      => $this->resolveValue($row, 'lastname') ?? null,
            'middlename'    => $this->resolveValue($row, 'middlename') ?? null,
            'phone'         => $this->resolveValue($row, 'phone') ?? null,
            'ip'            => $this->resolveValue($row, 'ip') ?? null,
            'email'         => $this->resolveValue($row, 'email') ?? null,
            'form_type'     => $this->resolveValue($row, 'form_type') ?? null,
            'utm_campaign'  => $this->resolveValue($row, 'utm_campaign') ?? null,
            'utm_source'    => $this->resolveValue($row, 'utm_source') ?? null,
            'utm_medium'    => $this->resolveValue($row, 'utm_medium') ?? null,
            'utm_content'   => $this->resolveValue($row, 'utm_content') ?? null,
            'utm_term'      => $this->resolveValue($row, 'utm_term') ?? null,
            'domain'        => $this->resolveValue($row, 'domain') ?? null,
            'clickid'       => $this->resolveValue($row, 'clickid') ?? null,
            'offer_id'      => $this->offer_id ?? null,
        ];

        if (!empty($this->affiliate_id)) {
            $mapped = array_merge($mapped, ['affiliate_id'  => $this->affiliate_id]);
        }

        return $mapped;
    }

    public function rules(): array
    {
        return [
            '*.api_key'       => ['sometimes'],
            '*.firstname'     => ['required'],
            '*.lastname'      => ['nullable'],
            '*.phone'         => ['required'],
            '*.ip'            => ['nullable'],
            '*.email'         => ['nullable', 'email'],
            '*.offer_id'      => Rule::requiredIf($this->offerIdRequired),
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '*.offer_id.required'     => 'API KEY отсутствует у аффилиатов, пожалуйста укажите оффера.',
        ];
    }

    /**
     * @param Collection $apiKeys
     *
     * @return void
     */
    public function validateApiKey(Collection $apiKeys)
    {
        $this->offerIdRequired = Affiliate::query()->whereIn('api_key', $apiKeys->toArray())->count() != $apiKeys->count();
    }

    /**
     * @param array  $row
     * @param string $headerKey
     *
     * @return mixed|null
     */
    protected function resolveValue(array $row, string $headerKey)
    {
        $value = null;

        if (isset($row[self::HEADERS[$headerKey]])) {
            $value = $row[self::HEADERS[$headerKey]];
        }

        return $value;
    }
}
