<?php

namespace App\Http\Requests\Affiliates;

use Illuminate\Foundation\Http\FormRequest;

class ExportLeads extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('view', $this->route('affiliate'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'since' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:now'],
            'until' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:since'],
        ];
    }
}
