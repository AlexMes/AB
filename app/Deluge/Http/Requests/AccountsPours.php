<?php

namespace App\Deluge\Http\Requests;

use App\ManualPour;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountsPours extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', ManualPour::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'accounts'      => ['required', 'array'],
            'accounts.*'    => Rule::exists('manual_accounts', 'account_id'),
            'date'          => ['required', 'date'],
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
            'accounts.required'    => ':attribute обязательны к заполнению.',
            'accounts.*.exists'    => 'Выбранный :attribute не существует.',
            'date.required'        => ':attribute обязательна к заполнению.',
            'date.date'            => ':attribute имеет не верный формат.',
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
            'accounts'   => 'Аккаунты',
            'accounts.*' => 'Аккаунт',
            'date'       => 'Дата',
        ];
    }
}
