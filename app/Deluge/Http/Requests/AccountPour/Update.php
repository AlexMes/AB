<?php

namespace App\Deluge\Http\Requests\AccountPour;

use App\ManualAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('pivot')->account);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status'            => ['required', 'string', Rule::in(ManualAccount::STATUSES)],
            'moderation_status' => ['required', 'string', Rule::in(ManualAccount::MODERATION_STATUSES)],
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
            'status.required'            => ':attribute обязателен к заполнению.',
            'status.in'                  => ':attribute имеет некорректное значение.',
            'moderation_status.required' => ':attribute обязателен к заполнению.',
            'moderation_status.in'       => ':attribute имеет некорректное значение.',
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
            'status'            => 'Статус',
            'moderation_status' => 'Статус модерации',
        ];
    }
}
