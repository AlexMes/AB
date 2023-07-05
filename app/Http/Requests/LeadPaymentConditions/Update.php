<?php

namespace App\Http\Requests\LeadPaymentConditions;

use App\LeadPaymentCondition;
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
            'office_id' => [
                'required',
                'integer',
                Rule::exists('offices', 'id'),
                Rule::unique('lead_payment_conditions')
                    ->where('offer_id', $this->input('offer_id'))
                    ->ignore($this->route('lead_payment_condition')),
            ],
            'offer_id'  => ['required', 'integer', Rule::exists('offers', 'id')],
            'model'     => ['required', 'string', 'max:255', Rule::in(LeadPaymentCondition::MODELS)],
            'cost'      => ['required', 'numeric', 'min:1'],
        ];
    }
}
