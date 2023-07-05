<?php

namespace App\Http\Requests\Tenants;

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
            'name'          => ['required', 'string', 'max:255'],
            'key'           => [
                'required',
                'string',
                'max:255',
                Rule::unique('tcrm_frx_tenants', 'key'),
            ],
            'url'           => ['required', 'url', 'max:255'],
            'client_id'     => ['required', 'integer'],
            'client_secret' => ['required', 'string', 'max:255'],
        ];
    }
}
