<?php

namespace App\Deluge\Http\Requests\Insights;

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
                Rule::unique('manual_insights', 'date')
                    ->where('account_id', $this->input('account_id'))
                    ->where('campaign_id', $this->input('campaign_id')),
            ],
            'account_id'     => ['required', 'string', Rule::exists('manual_accounts', 'account_id')],
            'campaign_id'    => ['required', 'string', Rule::exists('manual_campaigns', 'id')],
            'impressions'    => ['required', 'integer', 'min:0'],
            'clicks'         => ['required', 'integer', 'min:0'],
            'spend'          => ['required', 'numeric', 'min:0'],
            'leads_cnt'      => ['required', 'integer', 'min:0'],
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
            'date.unique'               => 'Статистика на связку :attribute + Аккаунт + Кампания уже существует.',
            'account_id.required'       => ':attribute обязателен к заполнению.',
            'account_id.exists'         => 'Выбранный :attribute не существует.',
            'campaign_id.required'      => ':attribute обязательна к заполнению.',
            'campaign_id.exists'        => 'Выбранная :attribute не существует.',
            'impressions.required'      => ':attribute обязательно к заполнению.',
            'impressions.integer'       => ':attribute должно быть целым числом.',
            'impressions.min'           => ':attribute должно быть не меньше :min.',
            'clicks.required'           => ':attribute обязательно к заполнению.',
            'clicks.integer'            => ':attribute должно быть целым числом.',
            'clicks.min'                => ':attribute должно быть не меньше :min.',
            'spend.required'            => ':attribute обязательно к заполнению.',
            'spend.numeric'             => ':attribute должно быть числом.',
            'spend.min'                 => ':attribute должно быть не меньше :min.',
            'leads_cnt.required'        => ':attribute обязательно к заполнению.',
            'leads_cnt.integer'         => ':attribute должно быть целым числом.',
            'leads_cnt.min'             => ':attribute должно быть не меньше :min.',
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
            'date'          => 'Дата',
            'account_id'    => 'Аккаунт',
            'campaign_id'   => 'Кампания',
            'impressions'   => 'Показы',
            'clicks'        => 'Клики',
            'spend'         => 'Кост',
            'leads_cnt'     => 'Лиды',
        ];
    }
}
