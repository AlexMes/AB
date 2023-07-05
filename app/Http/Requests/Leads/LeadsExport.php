<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadsExport extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'since'      => ['required', 'date_format:Y-m-d'],
            'until'      => ['required', 'date_format:Y-m-d'],
            'offer_id'   => ['sometimes', 'nullable', 'integer', Rule::exists('offers', 'id')],
            'branch_id'  => ['sometimes', 'nullable', 'integer', Rule::exists('branches', 'id')],
            'office_id'  => ['sometimes', 'nullable', 'integer', Rule::exists('offices', 'id')],
        ];
    }
}
