<?php

namespace App\CRM\Http\Requests;

use App\CRM\Label;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class CreateLabel extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Label::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:labels'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return Arr::except(trans('crm::validation'), ['attributes', 'custom']);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => trans('crm::label.form_name'),
        ];
    }
}
