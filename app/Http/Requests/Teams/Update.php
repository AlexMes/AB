<?php

namespace App\Http\Requests\Teams;

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
            'branch_id' => ['nullable', 'integer', Rule::exists('branches', 'id')],
        ];
    }
}
