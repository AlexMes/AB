<?php

namespace App\Gamble\Http\Requests\Accounts;

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
        /** @noinspection PhpParamsInspection */
        return [
            'account_id'    => [
                'required',
                'numeric',
                'digits_between:15,18',
                Rule::unique('gamble_accounts')->ignoreModel($this->route('account')),
            ],
            'name'              => ['required', 'string'],
            'user_id'           => ['nullable', 'integer', 'exists:users,id'],
            'group_id'          => ['array'],
            'group_id.*'        => ['integer', 'exists:gamble_groups,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'account_id.required'        => ':attribute обязателен к заполнению.',
            'account_id.unique'          => 'Указаный :attribute уже существует.',
            'account_id.numeric'         => ':attribute должен содержать только цифры.',
            'account_id.digits_between'  => ':attribute должен состоять из :min - :max цифр.',
            'name.required'              => ':attribute обязательно к заполнению.',
            'user_id.integer'            => ':attribute должен быть целым числом.',
            'user_id.exists'             => 'Выбранный :attribute не существует.',
            'group_id.array'             => ':attribute должа быть масивом.',
            'group_id.*.exists'          => 'Выбранная :attribute не существует.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'account_id'        => 'Аккаунт ID',
            'name'              => 'Название',
            'user_id'           => 'Байер',
            'group_id'          => 'Группа',
            'group_id.*'        => 'Группа',
        ];
    }
}
