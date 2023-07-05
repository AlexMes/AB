<?php

namespace App\Http\Requests\Telegram;

use Illuminate\Foundation\Http\FormRequest;

class BulkStatsCreate extends FormRequest
{
    public function authorize()
    {
        return $this->user()->isAdmin();
    }

    public function rules()
    {
        return [
            'date'                    => ['required','date'],
            'channels'                => ['required','array'],
            'channels.*.id'           => ['required','int','exists:telegram_channels,id'],
            'channels.*.cost'         => ['nullable','int'],
            'channels.*.impressions'  => ['nullable','int'],

        ];
    }
}
