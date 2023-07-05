<?php

namespace App\Http\Requests\StatusConfigs;

use App\CRM\Status;
use App\StatusConfig;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Create extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'office_id'         => ['required', 'integer', 'exists:offices,id'],
            'assigned_days_ago' => ['required', 'integer', 'min:1'],
            'new_status'        => ['required', 'string', Rule::in(Status::pluck('name'))],
            'statuses'          => ['required', 'array'],
            'statuses.*'        => ['string', Rule::in(Status::pluck('name'))],
            'statuses_type'     => ['required', 'string', Rule::in(StatusConfig::TYPES)],
            'enabled'           => ['required', 'boolean'],
        ];
    }
}
