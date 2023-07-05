<?php

namespace App\Http\Requests\ManualAccounts;

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
            'account_id'    => ['required', 'string', Rule::unique('manual_accounts')],
            'name'          => ['required', 'string'],
            'user_id'       => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
