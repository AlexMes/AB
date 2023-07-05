<?php

namespace App\Leads\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class Registration extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Validation rules for request
     *
     * @return \string[][]
     */
    public function rules()
    {
        return [
            'firstname'       => ['required','string','min:2','max:100'],
            'lastname'        => ['nullable','string','min:2','max:30'],
            'middlename'      => ['nullable','string','min:2','max:30'],
            'email'           => ['sometimes','email'],
            'phone'           => ['numeric','digits_between:8,15'],
            'ip'              => ['nullable','sometimes','ip'],
            'domain'          => ['string', 'nullable', 'max:150'],
            'form_type'       => ['nullable','string','min:2','max:100'],
            'utm_source'      => ['nullable','string','max:250'],
            'utm_campaign'    => ['nullable','string','max:250'],
            'utm_content'     => ['nullable','string','max:250'],
            'utm_term'        => ['nullable','string','max:250'],
            'utm_medium'      => ['nullable','string','max:250'],
            'clickid'         => ['string', 'nullable', 'max:255'],
            'api_key'         => ['nullable','exists:affiliates,api_key'],
            'poll'            => ['nullable','array'],
            'created_at'      => ['nullable','string'],
            'offer'           => ['nullable', 'string'],
        ];
    }
}
