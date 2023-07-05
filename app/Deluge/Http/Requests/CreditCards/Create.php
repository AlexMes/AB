<?php

namespace App\Deluge\Http\Requests\CreditCards;

use Illuminate\Foundation\Http\FormRequest;

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
            'bank_name'      => ['required', 'string', 'max:255'],
            'number'         => ['required', 'digits:16'],
            'account_id'     => ['required', 'exists:manual_accounts,account_id'],
            'buyer_id'       => ['required', 'integer', 'exists:users,id'],
            'social_profile' => ['nullable', 'string', 'max:255']
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
            'bank_name.required'    => 'Значение :attribute обязательно к заполнению.',
            'bank_name.string'      => 'Значение :attribute должно быть строкой.',
            'bank_name.max'         => 'Значение :attribute не должно превышать :max символов.',
            'number.required'       => 'Значение :attribute обязательно к заполнению.',
            'number.digits'         => 'Значение :attribute должно содержать :digits цифр.',
            'account_id.required'   => 'Значение :attribute обязательно к заполнению.',
            'account_id.exists'     => 'Значение :attribute не существует.',
            'buyer_id.required'     => 'Значение :attribute обязательно к заполнению.',
            'buyer_id.integer'      => ':attribute должен быть целым числом.',
            'buyer_id.exists'       => 'Выбранный :attribute не существует.',
            'social_profile.string' => 'Значение :attribute должно быть строкой.',
            'social_profile.max'    => 'Значение :attribute не должно превышать :max символов.',
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
            'bank_name'      => 'Банк',
            'number'         => 'Карта',
            'account_id'     => 'Аккаунт ID',
            'buyer_id'       => 'Байер',
            'social_profile' => 'Соц. профиль',
        ];
    }
}
