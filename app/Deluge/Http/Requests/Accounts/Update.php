<?php

namespace App\Deluge\Http\Requests\Accounts;

use App\ManualAccount;
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
            'account_id' => [
                'required',
                'numeric',
                'digits_between:9,20',
                Rule::unique('manual_accounts')->ignoreModel($this->route('account')),
            ],
            'name'              => ['required', 'string'],
            'user_id'           => ['nullable', 'integer', 'exists:users,id'],
            'status'            => ['required', 'string', Rule::in(ManualAccount::STATUSES)],
            'moderation_status' => ['required', 'string', Rule::in(ManualAccount::MODERATION_STATUSES)],
            'group_id'          => ['nullable', 'array'],
            'group_id.*'        => ['integer', 'exists:manual_groups,id'],
            'creo'              => ['nullable', 'string', 'min:5', 'max:150'],
            'supplier_id'       => ['required', 'integer', Rule::exists('manual_suppliers', 'id')],
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
            'status.required'            => ':attribute обязателен к заполнению.',
            'status.in'                  => ':attribute имеет некорректное значение.',
            'moderation_status.required' => ':attribute обязателен к заполнению.',
            'moderation_status.in'       => ':attribute имеет некорректное значение.',
            'group_id.array'             => ':attribute должа быть масивом.',
            'group_id.*.exists'          => 'Выбранная :attribute не существует.',
            'supplier_id.required'       => ':attribute обязателен к заполнению.',
            'supplier_id.exists'         => 'Выбранный :attribute не существует.',
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
            'status'            => 'Статус',
            'moderation_status' => 'Статус модерации',
            'group_id'          => 'Группа',
            'group_id.*'        => 'Группа',
            'supplier_id'       => 'Поставщик',
        ];
    }
}
