<?php

namespace App\Http\Requests\Branches;

use App\Enums\SmsService;
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
            'name'         => ['required', 'string', 'max:255'],
            'stats_access' => ['sometimes', 'boolean'],
            'telegram_id'  => ['nullable', 'numeric'],
            'sms_service'  => ['nullable', 'string', Rule::in(SmsService::LIST)],
            'sms_config'   => ['nullable', 'json'],
        ];
    }
}
