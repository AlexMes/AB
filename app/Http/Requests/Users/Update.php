<?php

namespace App\Http\Requests\Users;

use App\Rules\Lowercase;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    /**
     * Determine is current user is authorized for this request
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'         => 'required|string|min:2|max:40',
            'role'         => ['nullable', Rule::in(User::ROLES)],
            'email'        => 'required|email|unique:users,email,' . $this->route('user')->id,
            'telegram_id'  => 'nullable|numeric',
            'binomTag'     => [
                'nullable',
                'string',
                'unique:users,binomTag,' . $this->route('user')->id,
                new Lowercase()
            ],
            'showFbFields' => 'boolean',
            'branch_id'    => ['sometimes','nullable','exists:branches,id']
        ];
    }
}
