<?php

namespace App\Http\Requests\ProfileLogs;

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
            'link'         => ['required', 'url'],
            'duration'     => ['required', 'integer', 'min:0'],
            'profile_id'   => ['required', 'integer', 'exists:facebook_profiles,id'],
            'miniature'    => ['nullable', 'string'],
            'creative'     => ['nullable', 'string'],
            'text'         => ['nullable', 'string'],
        ];
    }
}
