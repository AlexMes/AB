<?php

namespace App\Gamble\Http\Requests\Campaigns;

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
            'campaign_id' => [
                'required',
                'numeric',
                'digits_between:13,20',
                Rule::unique('gamble_campaigns')->ignoreModel($this->route('campaign')),
            ],
            'name'       => ['required', 'string', 'max:255'],
            'account_id' => ['required', 'integer', Rule::exists('gamble_accounts', 'id')],
            'offer_id'   => ['required', 'integer', Rule::exists('gamble_offers', 'id')],
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
            'campaign_id.required'                 => ':attribute обязателен к заполнению.',
            'campaign_id.unique'                   => 'Указаный :attribute уже существует.',
            'campaign_id.numeric'                  => ':attribute должен содержать только цифры.',
            'campaign_id.digits_between'           => ':attribute должен состоять из :min - :max цифр.',
            'name.required'                        => ':attribute обязательно к заполнению.',
            'account_id.required'                  => ':attribute обязателен к заполнению.',
            'account_id.exists'                    => 'Выбранный :attribute не существует.',
            'offer_id.required'                    => ':attribute обязателен к заполнению.',
            'offer_id.exists'                      => 'Выбранный :attribute не существует.',
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
            'campaign_id'   => 'ID кампании',
            'name'          => 'Название',
            'account_id'    => 'Аккаунт',
            'offer_id'      => 'Офер',
        ];
    }
}
