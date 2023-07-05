<?php

namespace App\CRM\Http\Requests;

use App\CRM\LeadOrderAssignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class MarkLeftoverAssignment extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('markAsLeftover', $this->route('assignment'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var LeadOrderAssignment $assignment */
        $assignment = $this->route('assignment');

        throw_if(
            $assignment->route->offer->isLeftover(),
            ValidationException::withMessages(['offer_id' => trans('crm::validation.assignment_is_already_leftover')])
        );

        return [
            //
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
