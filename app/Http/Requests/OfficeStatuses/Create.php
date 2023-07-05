<?php

namespace App\Http\Requests\OfficeStatuses;

use App\CRM\Status;
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
            'office_id'  => ['required', 'integer', 'exists:offices,id'],
            'status'     => ['required', 'string', Rule::in(Status::pluck('name'))],
            'selectable' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
