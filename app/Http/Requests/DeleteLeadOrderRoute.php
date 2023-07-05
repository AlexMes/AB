<?php

namespace App\Http\Requests;

use App\LeadOrderRoute;
use App\Rules\RouteAssignmentsDistribution;
use App\Rules\TransferManagerShouldHaveSameOffice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteLeadOrderRoute extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('delete', $this->route('route'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var LeadOrderRoute $route */
        $route = $this->route('route');

        return [
            'action'     => [
                'nullable',
                'present',
                'string',
                Rule::in(['leftovers', 'delete_leads', 'transfer', 'distribute']),
                new RouteAssignmentsDistribution($route),
            ],
            'manager_id' => [
                'exclude_unless:action,transfer',
                'exists:managers,id',
                new TransferManagerShouldHaveSameOffice($route->order),
            ],
        ];
    }
}
