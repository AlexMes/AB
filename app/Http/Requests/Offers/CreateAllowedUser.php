<?php

namespace App\Http\Requests\Offers;

use Illuminate\Foundation\Http\FormRequest;

class CreateAllowedUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin() || $this->user()->isDeveloper();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'  => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
