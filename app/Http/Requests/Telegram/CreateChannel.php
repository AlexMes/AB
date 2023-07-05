<?php

namespace App\Http\Requests\Telegram;

use Illuminate\Foundation\Http\FormRequest;

class CreateChannel extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('store', \App\TelegramChannel::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'       => ['required','string','min:2','max:255'],
            'subject_id' => ['nullable','exists:telegram_channel_subjects,id']
        ];
    }
}
