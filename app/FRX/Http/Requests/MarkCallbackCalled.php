<?php

namespace App\FRX\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkCallbackCalled extends FormRequest
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
            'called_at' => ['sometimes', 'required', 'date', 'before_or_equal:now'],
            'call_id'   => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}
