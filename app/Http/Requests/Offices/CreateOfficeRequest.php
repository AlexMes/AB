<?php

namespace App\Http\Requests\Offices;

use App\Office;
use Illuminate\Foundation\Http\FormRequest;

class CreateOfficeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Office::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                 => 'required|string|min:1|max:40',
            /*'cpl'            => ['required','numeric','min:0'],
            'cpa'            => ['required','numeric','min:0'],*/
            'destination_id'       => ['nullable','int', 'exists:lead_destinations,id'],
            'allow_transfer'       => ['sometimes', 'boolean'],
            'distribution_enabled' => ['sometimes', 'boolean'],
            'default_start_at'     => ['nullable', 'date_format:H:i'],
            'default_stop_at'      => ['nullable', 'date_format:H:i'],
            'frx_office_id'        => ['nullable', 'integer', 'min:1'],
            'frx_tenant_id'        => ['nullable', 'integer', 'exists:tcrm_frx_tenants,id'],
            'is_cp'                => ['sometimes', 'boolean'],
            'morning_branches'     => ['array'],
            'morning_branches.*'   => ['integer', 'exists:branches,id'],
            'disallow_caucasian'   => ['sometimes', 'boolean'],
        ];
    }
}
