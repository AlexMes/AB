<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateBlackLead extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'branch_id' => ['required', 'integer', Rule::exists('branches', 'id')],
            'phone'     => ['required', 'digits_between:8,15'],
        ];
    }
}
