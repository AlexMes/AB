<?php

namespace App\Gamble\Http\Requests\Applications;

use App\Gamble\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateLink extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'   => [
                'required',
                'integer',
                Rule::exists('users', 'id')->whereIn('role', [
                    User::ADMIN, User::GAMBLE_ADMIN, User::GAMBLER
                ]),
            ],
            'enabled'   => ['required', 'boolean'],
            'url'       => ['required', 'string', 'url', 'max:255'],
            'geo'       => ['nullable', 'string', 'max:255'],
        ];
    }
}
