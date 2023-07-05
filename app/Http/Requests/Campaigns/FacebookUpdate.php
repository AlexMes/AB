<?php

namespace App\Http\Requests\Campaigns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FacebookUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('campaign'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'budget'        => 'required|numeric',
            'budget_field'  => [
                'required',
                'string',
                Rule::in('daily_budget', 'lifetime_budget'),
            ],
        ];
    }
}
