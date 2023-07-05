<?php

namespace App\Gamble\Http\Requests\TechCosts;

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
            'date'    => [
                'required',
                'date',
                'before_or_equal:now',
                Rule::unique('gamble_tech_costs', 'date')
                    ->where('user_id', $this->input('user_id'))
                    ->ignore($this->route('tech_cost')),
            ],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'spend'   => ['required', 'numeric', 'min:0'],
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
            'date.required'        => ':attribute обязательна к заполнению.',
            'date.date'            => ':attribute имеет не верный формат.',
            'date.before_or_equal' => ':attribute должна быть сегодняшней или раньше.',
            'date.unique'          => 'Запись на связку :attribute + Пользователь уже существует.',
            'user_id.required'     => ':attribute обязателен к заполнению.',
            'user_id.integer'      => ':attribute должен быть целым числом.',
            'user_id.exists'       => 'Выбранный :attribute не существует.',
            'spend.required'       => ':attribute обязательно к заполнению.',
            'spend.numeric'        => ':attribute должно быть числом.',
            'spend.min'            => ':attribute должно быть не меньше :min.',
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
            'date'    => 'Дата',
            'user_id' => 'Пользователь',
            'spend'   => 'Кост',
        ];
    }
}
