<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class MassStoreDomains extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'urls'                => ['required', 'array'],
            'urls.*'              => ['required', 'url'],
            'status'              => ['required', 'string'],
            'linkType'            => ['required','string'],
            'user_id'             => ['numeric', 'nullable', 'exists:users,id'],
            'splitterEnabled'     => ['required', 'boolean'],
        ];
    }
}
