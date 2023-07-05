<?php

namespace App\Http\Requests\OfficePayments;

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
            'office_id' => ['required', 'integer', 'exists:offices,id'],
            'paid'      => ['required', 'integer'],
            /*'assigned'  => ['sometimes', 'required', 'integer'],*/
        ];
    }
}
