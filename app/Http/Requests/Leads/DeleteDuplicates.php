<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteDuplicates extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin() || $this->user()->isSupport() && $this->user()->branch_id === 19;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'since'    => ['required', 'date_format:Y-m-d'],
            'until'    => ['required', 'date_format:Y-m-d'],
            'offers'   => ['required', 'array'],
            'offers.*' => ['required', 'integer', Rule::exists('offers', 'id')],
        ];
    }
}
