<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeLeftoversOffer extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin() || $this->user()->isSupport() && $this->user()->branch_id === 19;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'since'      => ['sometimes', Rule::requiredIf(!$this->hasFile('leads_file')), 'date_format:Y-m-d'],
            'until'      => ['sometimes', Rule::requiredIf(!$this->hasFile('leads_file')), 'date_format:Y-m-d'],
            'offer_from' => [
                'sometimes',
                Rule::requiredIf(!$this->hasFile('leads_file')),
                'integer',
                Rule::exists('offers', 'id')
            ],
            'offer_to'   => [
                'sometimes',
                Rule::requiredIf(!$this->hasFile('leads_file')),
                'integer',
                Rule::exists('offers', 'id')
            ],
            'leads_file' => ['nullable', 'file'],
        ];
    }
}
