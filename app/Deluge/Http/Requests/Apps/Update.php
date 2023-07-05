<?php

namespace App\Deluge\Http\Requests\Apps;

use App\ManualApp;
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
            'link'    => ['required', 'url', Rule::unique('manual_apps')->ignore($this->route('app'))],
            'status'  => ['required', Rule::in(ManualApp::STATUSES)],
            'chat_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
