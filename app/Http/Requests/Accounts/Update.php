<?php

namespace App\Http\Requests\Accounts;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine is user authorized to make request
     *
     * @return mixed
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('account'));
    }

    /**
     * Validation rules
     *
     * @return array
     */
    public function rules()
    {
        return  [
            'group_id'        => ['nullable','int','exists:groups,id'],
            'comment'         => ['nullable','string', 'min:3', 'max:255'],
            'stopper_enabled' => ['sometimes', 'boolean'],
        ];
    }
}
