<?php

namespace App\Http\Requests\Profiles;

use App\Facebook\Profile;
use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine is user can update profile
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('profile'));
    }

    /**
     * Defines validation rules for the request
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'  => ['nullable','int','exists:users,id'],
            'group_id' => ['nullable','int','exists:groups,id'],
        ];
    }
}
