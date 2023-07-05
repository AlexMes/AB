<?php

namespace App\Deluge\Http\Requests\Campaigns;

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
            'id'         => ['required', 'numeric', 'digits_between:13,20', Rule::unique('manual_campaigns')],
            'name'       => ['required', 'string', 'max:255'],
            'account_id' => ['required', 'string', Rule::exists('manual_accounts', 'account_id')],
            'bundle_id'  => ['required', 'integer', 'exists:manual_bundles,id'],
            'creo'       => ['nullable', 'string', 'min:5', 'max:150'],
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
            'id.required'         => ':attribute обязателен к заполнению.',
            'id.unique'           => 'Указаный :attribute уже существует.',
            'id.numeric'          => ':attribute должен содержать только цифры.',
            'id.digits_between'   => ':attribute должен состоять из :min - :max цифр.',
            'name.required'       => ':attribute обязательно к заполнению.',
            'account_id.required' => ':attribute обязателен к заполнению.',
            'account_id.exists'   => 'Выбранный :attribute не существует.',
            'bundle_id.required'  => ':attribute обязательна к заполнению.',
            'bundle_id.integer'   => ':attribute должна быть целым числом.',
            'bundle_id.exists'    => 'Выбранная :attribute не существует.',
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
            'id'         => 'ID кампании',
            'name'       => 'Название',
            'account_id' => 'Аккаунт',
            'bundle_id'  => 'Связка',
        ];
    }
}
