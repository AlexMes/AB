<?php

namespace App\Http\Requests\ManualAccounts;

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
            'account_id'    => ['required', 'string',Rule::unique('manual_accounts')->ignoreModel($this->route('manual_account'))],
            'name'          => ['required', 'string'],
            'user_id'       => ['nullable', 'integer', 'exists:users,id'],
            'creo'          => ['nullable','string','min:5','max:150']
        ];
    }
}
