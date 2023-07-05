<?php

namespace App\Http\Requests\OfficeGroups;

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
            'name'      => ['required', 'string', 'max:255'],
            'branch_id' => ['required', 'integer', Rule::exists('branches', 'id')],
        ];
    }
}
