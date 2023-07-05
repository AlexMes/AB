<?php

namespace App\Deluge\Http\Requests\Unity\Organizations;

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
        $organization = $this->route('organization');

        return [
            'name'                  => ['required', 'string', 'max:255'],
            'organization_core_id'  => [
                'required',
                'numeric',
                Rule::unique('unity_organizations')->ignore($organization),
            ],
            'organization_id'       => [
                'required',
                'alpha_num',
                Rule::unique('unity_organizations')->ignore($organization),
            ],
            'api_key'               => ['required', 'string', 'max:255'],
            'key_id'                => ['required', 'string', 'max:255'],
            'secret_key'            => ['required', 'string', 'max:255'],
        ];
    }
}
