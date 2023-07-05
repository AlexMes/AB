<?php

namespace App\Http\Requests\LeadsDestinations;

use App\CRM\Status;
use App\LeadDestination;
use App\LeadDestinationDriver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Create extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->id === 1 || $this->user()->isSupport();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'driver'                => ['required', 'string', Rule::in(LeadDestinationDriver::pluck('id'))],
            'autologin'             => ['required', 'string', Rule::in(LeadDestination::AUTOLOGIN_OPTIONS)],
            'configuration'         => ['nullable', 'json'],
            'branch_id'             => ['nullable', 'integer', Rule::exists('branches', 'id')],
            'office_id'             => ['nullable', 'integer', Rule::exists('offices', 'id')],
            'status_map'            => ['nullable', 'array'],
            'status_map.*.external' => ['required', 'string', 'distinct'],
            'status_map.*.internal' => ['nullable', 'string', Rule::in(Status::pluck('name'))],
            'is_active'             => ['required', 'boolean'],
            'land_autologin'        => ['required', 'boolean'],
            'deposit_notification'  => ['required', 'boolean'],
        ];
    }
}
