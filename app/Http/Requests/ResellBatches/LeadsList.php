<?php

namespace App\Http\Requests\ResellBatches;

use App\CRM\Age;
use App\CRM\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadsList extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin()
            || $this->user()->id === 27
            || in_array($this->user()->branch_id, [20, 16])
            || $this->user()->isSupport() && $this->user()->branch_id === 19;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
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
        ];
    }
}
