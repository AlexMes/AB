<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'            => ['nullable', 'email', 'max:255'],
            'firstname'        => ['required', 'string', 'min:2', 'max:100'],
            'lastname'         => ['nullable', 'string', 'min:2', 'max:30'],
            'middlename'       => ['nullable', 'string', 'min:2', 'max:30'],
            'offer_id'         => ['nullable', 'integer', Rule::exists('offers', 'id')],
            'ip'               => ['required', 'ip'],
            'valid'            => ['sometimes', 'required', 'boolean'],
            'recreate_deposit' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
