<?php

namespace App\Deluge\Http\Requests\Bundles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name'          => [
                'required',
                'string',
                Rule::unique('manual_bundles', 'name')->ignoreModel($this->route('bundle'))
            ],
            'offer_id'          => ['required', 'integer', 'exists:offers,id'],
            'traffic_source_id' => ['sometimes', 'nullable', 'exists:manual_traffic_sources,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'              => ':attribute обязательно к заполнению.',
            'offer_id.integer'           => ':attribute должен быть целым числом.',
            'offer_id.exists'            => 'Выбранный :attribute не существует.',
            'traffic_source_id.integer'  => ':attribute должен быть целым числом.',
            'traffic_source_id.exists'   => 'Выбранный :attribute не существует.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name'              => 'Название',
            'offer_id'          => 'Офер',
            'traffic_source_id' => 'Источник',
        ];
    }
}
