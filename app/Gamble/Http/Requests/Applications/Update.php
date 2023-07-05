<?php

namespace App\Gamble\Http\Requests\Applications;

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
            'name'      => [
                'required',
                'string',
                'max:255',
                Rule::unique('google_apps', 'name')->ignoreModel($this->route('application')),
            ],
            'market_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('google_apps', 'market_id')->ignoreModel($this->route('application')),
            ],
            'enabled'       => ['required', 'boolean'],
            'url'           => ['required', 'string', 'url', 'max:255'],
            'geo'           => ['nullable', 'string', 'max:255'],
            'fb_app_id'     => ['nullable','string','max:255'],
            'fb_app_secret' => ['nullable','string','max:255'],
            'fb_token'      => ['nullable','string','max:255'],
        ];
    }
}
