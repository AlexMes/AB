<?php

namespace App\Http\Requests;

use App\Manager;
use Illuminate\Foundation\Http\FormRequest;

class CreateOfficeManager extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Manager::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:managers,email'],
            'spreadsheet_id' => ['nullable', 'string', 'max:255'],
            'frx_role'       => ['sometimes', 'required', 'string', 'max:255'],
            'password'       => ['required_with:frx_role', 'confirmed', 'min:8'],
        ];
    }
}
