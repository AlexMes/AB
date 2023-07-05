<?php

namespace App\Http\Requests\Access;

use App\Facebook\Rules\ValidProfileUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    /**
     * Authorize request
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('access'));
    }

    /**
     * Validation rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            'received_at'    => ['required','date'],
            'birthday'       => ['nullable','date'],
            'user_id'        => ['nullable','int','exists:users,id'],
            'supplier_id'    => ['required','int','exists:access_suppliers,id'],
            'type'           => ['required','string',Rule::in(['farm','brut','own'])],
            'facebook_url'   => ['required','url', new ValidProfileUrl()],
            'login'          => ['required','string'],
            'password'       => ['nullable','string'],
            'email'          => ['nullable','email'],
            'email_password' => ['nullable','string'],
            'profile_name'   => ['nullable', 'string'],
        ];
    }
}
