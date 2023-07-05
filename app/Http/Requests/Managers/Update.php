<?php

namespace App\Http\Requests\Managers;

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
            'name'           => ['required', 'string', 'max:255'],
            'email'          => [
                'required',
                'email',
                'max:255',
                Rule::unique('managers', 'email')->ignoreModel($this->route('manager')),
            ],
            'spreadsheet_id' => ['nullable', 'string', 'max:255'],
            'office_id'      => [
                'required_if:is_frx,false',
                'exclude_unless:is_frx,false',
                'integer',
                'exists:offices,id',
            ],
            'frx_role'       => [
                'required_if:is_frx,false',
                'exclude_unless:is_frx,false',
                'string',
                'max:255',
                Rule::in(['root', 'admin', 'closer', 'agent']),
            ],
        ];
    }
}
