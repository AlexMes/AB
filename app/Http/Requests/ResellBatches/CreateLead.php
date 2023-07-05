<?php

namespace App\Http\Requests\ResellBatches;

use App\CRM\Age;
use App\CRM\Status;
use App\ResellBatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateLead extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('resell_batch'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var ResellBatch $batch */
        $batch = $this->route('resell_batch');
        throw_if(
            $batch->status !== 'pending',
            ValidationException::withMessages(['leads' => 'Leads cannot be changed after start.'])
        );

        return [
            'registered_at'   => ['required', 'array', 'size:2'],
            'registered_at.1' => ['date_format:Y-m-d', 'before_or_equal:now'],
            'registered_at.0' => ['date_format:Y-m-d', 'before_or_equal:registered_at.1'],
            'created_at'      => ['nullable', 'array', 'size:2'],
            'created_at.1'    => ['date_format:Y-m-d', 'before_or_equal:now'],
            'created_at.0'    => ['date_format:Y-m-d', 'before_or_equal:created_at.1'],
            'office'          => ['array'],
            'office.*'        => [Rule::exists('offices', 'id')],
            'offer'           => ['array'],
            'offer.*'         => [Rule::exists('offers', 'id')],
            'status'          => ['array'],
            'status.*'        => [Rule::in(Status::pluck('name'))],
            'age'             => ['array'],
            'age.*'           => [Rule::in(Age::pluck('name'))],
            'leads'           => ['required', 'array'],
            'leads.*'         => ['integer', Rule::exists('leads', 'id')],
        ];
    }
}
