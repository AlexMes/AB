<?php

namespace App\Http\Requests\OfficeGroups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttachOffice extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('office_group'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'office_id' => ['required', 'integer', Rule::exists('offices', 'id')],
        ];
    }
}
