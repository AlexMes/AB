<?php

namespace App\Integrations\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormRequest extends FormRequest
{
    /**
     * Validation rules
     *
     * @return array
     */
    public function rules()
    {
        return  [
            'name'                      => ['required', 'string'],
            'url'                       => ['required', 'string'],
            'method'                    => ['required', 'string'],
            'form_id'                   => ['sometimes', 'nullable', 'numeric'],
            'form_api_key'              => ['sometimes', 'nullable', 'string'],
            'provider'                  => ['required', 'string'],
            'phone_prefix'              => ['sometimes', 'nullable', 'string'],
            'external_affiliate_id'     => ['sometimes', 'nullable', 'string'],
            'external_offer_id'         => ['sometimes', 'nullable', 'string'],
            'fields'                    => ['sometimes', 'nullable', 'array'],
            'status'                    => 'boolean',
            'landing_id'                => ['required','int','exists:domains,id'],
        ];
    }
}
