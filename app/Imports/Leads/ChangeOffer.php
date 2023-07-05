<?php

namespace App\Imports\Leads;

use App\Lead;
use App\Offer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

class ChangeOffer implements ToCollection, WithHeadingRow, WithMapping
{
    use Importable;

    protected const HEADERS = [
        'id'   => 'ID',
        'from' => 'from',
        'to'   => 'to',
    ];

    /**
     * @inheritDoc
     */
    public function collection(Collection $rows)
    {
        $validator = Validator::make($rows->toArray(), $this->rules());
        if ($validator->fails()) {
            throw ValidationException::withMessages(['leads_file' => $validator->errors()->messages()]);
        }

        $offers = Offer::whereIn('name', $rows->pluck('from')->merge($rows->pluck('to')))
            ->get(['id', 'name'])
            ->mapWithKeys(fn ($offer) => [$offer->name => $offer->id]);

        $rows->each(function ($row) use ($offers) {
            Lead::visible()
                ->where(function (Builder $builder) use ($row) {
                    return $builder->where(DB::raw('id::text'), $row['id'])
                        ->orWhere('phone', $row['id'])
                        ->orWhere('email', $row['id']);
                })
                ->whereOfferId($offers->get($row['from']))
                ->get()->each(function (Lead $lead) use ($row, $offers) {
                    if ($offers->has($row['to'])) {
                        $lead->update(['offer_id' => $offers->get($row['to'])]);
                    }
                });
        });
    }

    /**
     * @inheritDoc
     */
    public function map($row): array
    {
        return [
            'id'   => $this->resolveValue($row, 'id') ?? null,
            'from' => $this->resolveValue($row, 'from') ?? null,
            'to'   => $this->resolveValue($row, 'to') ?? null,
        ];
    }

    public function rules(): array
    {
        return [
            '*.id'   => ['required'],
            '*.from' => ['required'],
            '*.to'   => ['required'],
        ];
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
