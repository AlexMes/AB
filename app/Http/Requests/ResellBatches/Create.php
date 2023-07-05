<?php

namespace App\Http\Requests\ResellBatches;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Create extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                 => ['required', 'string', 'max:255'],
            'registered_at'        => ['required', 'date_format:Y-m-d', 'before_or_equal:now'],
            'offices'              => ['nullable', 'array'],
            'offices.*'            => ['integer', Rule::exists('offices', 'id')],
            'substitute_offer'     => ['nullable'],
            'create_offer'         => ['sometimes', 'boolean'],
            'simulate_autologin'   => ['sometimes', 'boolean'],
            'ignore_paused_routes' => ['sometimes', 'boolean'],
            'filters'              => ['sometimes', 'array'],
        ];
    }

    public function validated()
    {
        return array_merge(['branch_id' => auth()->user()->branch_id], parent::validated());
    }
}
