<?php

namespace App\Http\Requests\Pages;

use Illuminate\Foundation\Http\FormRequest;

class CreatePageRequest extends FormRequest
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
            'name'        => ['required', 'string', 'min:3', 'max:40'],
            'type'        => ['required', 'string'],
            'repository'  => ['sometimes', 'nullable', 'string'],
        ];
    }
}
