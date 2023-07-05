<?php

namespace App\Http\Requests\Managers;

use Illuminate\Foundation\Http\FormRequest;

class ChangeOffice extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin() || $this->user()->isSupport() || $this->user()->isDeveloper();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'office_id' => ['required', 'integer', 'exists:offices,id'],
        ];
    }
}
