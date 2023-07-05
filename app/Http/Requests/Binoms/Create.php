<?php

namespace App\Http\Requests\Binoms;

use Illuminate\Foundation\Http\FormRequest;

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
            'name'              => ['required', 'string', 'max:255'],
            'enabled'           => ['required', 'boolean'],
            'url'               => ['required', 'string', 'url', 'max:255'],
            'access_token'      => ['required', 'string', 'max:255'],
        ];
    }
}
