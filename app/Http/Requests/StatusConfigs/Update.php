<?php

namespace App\Http\Requests\StatusConfigs;

use App\CRM\Status;
use App\StatusConfig;
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
            'office_id'         => ['sometimes', 'required', 'integer', 'exists:offices,id'],
            'assigned_days_ago' => ['sometimes', 'required', 'integer', 'min:1'],
            'new_status'        => ['sometimes', 'required', 'string', Rule::in(Status::pluck('name'))],
            'statuses'          => ['sometimes', 'required', 'array'],
            'statuses.*'        => ['string', Rule::in(Status::pluck('name'))],
            'statuses_type'     => ['sometimes', 'required', 'string', Rule::in(StatusConfig::TYPES)],
            'enabled'           => ['sometimes', 'required', 'boolean'],
        ];
    }
}
