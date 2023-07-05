<?php

namespace App\Http\Requests\OfficeStatuses;

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
            'selectable' => ['required', 'boolean'],
        ];
    }
}
