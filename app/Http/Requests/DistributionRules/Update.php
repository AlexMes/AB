<?php

namespace App\Http\Requests\DistributionRules;

use Illuminate\Foundation\Http\FormRequest;

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
            'office_id'     => ['sometimes', 'nullable', 'integer', 'exists:offices,id'],
            'offer_id'      => ['sometimes', 'nullable', 'integer', 'exists:offers,id'],
            'geo'           => ['sometimes', 'required', 'string'],
            'country_name'  => ['sometimes', 'required', 'string'],
            'allowed'       => [
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
