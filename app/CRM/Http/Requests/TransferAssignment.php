<?php

namespace App\CRM\Http\Requests;

use App\LeadOrderRoute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class TransferAssignment extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('transfer', $this->route('assignment'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var LeadOrderRoute $route */
        $route = $this->route('assignment')->route;

        return [
            'manager_id' => [
                'required',
                Rule::exists('managers', 'id')
                    ->where('office_id', $route->order->office_id)
                    ->whereNot('id', $route->manager_id),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return Arr::except(trans('crm::validation'), ['attributes', 'custom']);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('crm::validation.attributes');
    }
}
