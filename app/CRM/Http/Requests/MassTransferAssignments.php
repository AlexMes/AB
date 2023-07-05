<?php

namespace App\CRM\Http\Requests;

use App\CRM\LeadOrderAssignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MassTransferAssignments extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('massTransfer', LeadOrderAssignment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $offices = LeadOrderAssignment::with('route.order')
            ->whereIn('id', $this->input('assignments', []))
            ->get()
            ->pluck('route.order.office_id')
            ->unique();

        throw_if(
            $offices->count() > 1,
            ValidationException::withMessages(['manager_id' => trans('crm::validation.all_assignments_in_one_office')])
        );

        return [
            'manager_id' => [
                'required',
                Rule::exists('managers', 'id')
                    ->where('office_id', $offices->first()),
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
