<?php

namespace App\Http\Requests\VKProfiles;

use Illuminate\Foundation\Http\FormRequest;

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
            'user_id'  => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
