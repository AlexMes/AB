<?php

namespace App\Http\Requests\DistributionRules;

use Illuminate\Foundation\Http\FormRequest;

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
            'office_id'     => ['nullable', 'integer', 'exists:offices,id'],
            'offer_id'      => ['nullable', 'integer', 'exists:offers,id'],
            'geo'           => ['required', 'string'],
            'country_name'  => ['required', 'string'],
            'allowed'       => [
                'sometimes',
                'required',
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value && !$this->filled('office_id')) {
                        $fail('Global rule cannot be allowed.');
                    }
                },
            ],
        ];
    }
}
