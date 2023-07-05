<?php

namespace App\Http\Requests;

use App\LeadPaymentCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MassSetLeadBenefit extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin() || $this->user()->isSupport();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'office'   => ['required', 'integer', Rule::exists('offices', 'id')],
            'offer'    => ['required', 'integer', Rule::exists('offers', 'id')],
            'since'    => ['nullable', 'date_format:Y-m-d', 'before_or_equal:until'],
            'until'    => ['nullable', 'date_format:Y-m-d', 'after_or_equal:since'],
            'benefit'  => ['required', 'numeric', 'min:0'],
            'to_model' => ['required', 'string', Rule::in(LeadPaymentCondition::MODELS)],
        ];
    }
}
