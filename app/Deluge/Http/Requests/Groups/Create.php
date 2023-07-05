<?php

namespace App\Deluge\Http\Requests\Groups;

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
            'name'      => ['required', 'string'],
            'branch_id' => ['nullable', 'integer', Rule::exists('branches', 'id')],
            'google'    => ['nullable', 'string', 'max:255'],
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
            'name.required'     => ':attribute обязательно к заполнению.',
            'branch_id.integer' => ':attribute должен быть целым числом.',
            'branch_id.exists'  => 'Выбранный :attribute не существует.',
            'google.max'        => ':attribute не может превышать :max символов',
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
            'name'      => 'Название',
            'branch_id' => 'Филиал',
        ];
    }
}
