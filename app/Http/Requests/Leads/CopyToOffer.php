<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CopyToOffer extends FormRequest
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
            'offer_from' => ['required', 'integer', Rule::exists('offers', 'id')],
            'offer_to'   => ['required', 'integer', Rule::exists('offers', 'id')],
            'user_to'    => ['required', 'integer', Rule::exists('users', 'id')],
            'domain_to'  => ['required', 'string'],
            'amount'     => ['required', 'integer', 'min:1'],
            'country'    => ['nullable', Rule::exists('ip_addresses', 'country_name')],
        ];
    }
}
