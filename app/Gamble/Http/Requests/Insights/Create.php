<?php

namespace App\Gamble\Http\Requests\Insights;

use App\Gamble\Insight;
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
            'date'           => [
                'required',
                'date',
                'before_or_equal:now',
                Rule::unique('gamble_insights', 'date')
                    ->where('account_id', $this->input('account_id'))
                    ->where('campaign_id', $this->input('campaign_id')),
            ],
            'account_id'        => ['required', 'integer', Rule::exists('gamble_accounts', 'id')],
            'campaign_id'       => ['required', 'integer', Rule::exists('gamble_campaigns', 'id')],
            'google_app_id'     => ['required', 'integer', Rule::exists('google_apps', 'id')],
            'pour_type'         => ['required', 'string', Rule::in(Insight::POUR_TYPES)],
            'target'            => ['required', 'string'],
            'sales'             => ['required', 'integer', 'min:0'],
            'deposit_cnt'       => ['required', 'integer', 'min:0'],
            'deposit_sum'       => ['required', 'numeric', 'min:0'],
            'impressions'       => ['required', 'integer', 'min:0'],
            'installs'          => ['required', 'integer', 'min:0'],
            'spend'             => ['required', 'numeric', 'min:0'],
            'registrations'     => ['required', 'integer', 'min:0'],
            'optimization_goal' => ['nullable', 'string', 'max:255'],
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
            'date.required'             => ':attribute обязательная к заполнению.',
            'date.date'                 => ':attribute имеет не верный формат.',
            'date.before_or_equal'      => ':attribute должна быть сегодняшней или раньше.',
            'date.unique'               => 'Запись на связку :attribute + Аккаунт + Кампания уже существует.',
            'account_id.required'       => ':attribute обязателен к заполнению.',
            'account_id.integer'        => ':attribute должен быть целым числом.',
            'account_id.exists'         => 'Выбранный :attribute не существует.',
            'campaign_id.required'      => ':attribute обязательна к заполнению.',
            'campaign_id.integer'       => ':attribute должена быть целым числом.',
            'campaign_id.exists'        => 'Выбранная :attribute не существует.',
            'google_app_id.required'    => ':attribute обязательно к заполнению.',
            'google_app_id.integer'     => ':attribute должено быть целым числом.',
            'google_app_id.exists'      => 'Выбранное :attribute не существует.',
            'pour_type.required'        => ':attribute обязателен к заполнению.',
            'pour_type.string'          => ':attribute должен быть строкой.',
            'pour_type.in'              => 'Выбранный :attribute некорректен.',
            'target.required'           => ':attribute обязателен к заполнению.',
            'target.string'             => ':attribute должен быть строкой.',
            'sales.required'            => ':attribute обязательно к заполнению.',
            'sales.integer'             => ':attribute должно быть целым числом.',
            'sales.min'                 => ':attribute должно быть не меньше :min.',
            'deposit_cnt.required'      => ':attribute обязательно к заполнению.',
            'deposit_cnt.integer'       => ':attribute должно быть целым числом.',
            'deposit_cnt.min'           => ':attribute должно быть не меньше :min.',
            'deposit_sum.required'      => ':attribute обязательна к заполнению.',
            'deposit_sum.numeric'       => ':attribute должна быть числом.',
            'deposit_sum.min'           => ':attribute должна быть не меньше :min.',
            'impressions.required'      => ':attribute обязательно к заполнению.',
            'impressions.integer'       => ':attribute должно быть целым числом.',
            'impressions.min'           => ':attribute должно быть не меньше :min.',
            'installs.required'         => ':attribute обязательно к заполнению.',
            'installs.integer'          => ':attribute должно быть целым числом.',
            'installs.min'              => ':attribute должно быть не меньше :min.',
            'spend.required'            => ':attribute обязательно к заполнению.',
            'spend.numeric'             => ':attribute должно быть числом.',
            'spend.min'                 => ':attribute должно быть не меньше :min.',
            'registrations.required'    => ':attribute обязательно к заполнению.',
            'registrations.integer'     => ':attribute должно быть целым числом.',
            'registrations.min'         => ':attribute должно быть не меньше :min.',
            'optimization_goal.string'  => ':attribute должна быть строкой.',
            'optimization_goal.max'     => ':attribute должно быть не больше :max символов.',
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
            'date'              => 'Дата',
            'account_id'        => 'Аккаунт',
            'campaign_id'       => 'Кампания',
            'google_app_id'     => 'Приложение',
            'pour_type'         => 'Способ залива',
            'target'            => 'Таргет',
            'sales'             => 'Продажи',
            'deposit_cnt'       => 'Количество депозитов',
            'deposit_sum'       => 'Сумма депозита',
            'impressions'       => 'Показы',
            'installs'          => 'Установки',
            'spend'             => 'Кост',
            'registrations'     => 'Регистрации',
            'optimization_goal' => 'Цель оптимизации',
        ];
    }
}
