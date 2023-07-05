<?php

namespace App\Deluge\Http\Requests\Unity\Apps;

use App\UnityApp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'id'              => ['required', 'string', Rule::unique('unity_apps')],
            'name'            => ['required', 'string', 'max:255'],
            'store'           => ['required', 'string', Rule::in(UnityApp::STORES)],
            'store_id'        => ['nullable', 'string', 'max:255'],
            'adomain'         => ['nullable', 'string', 'max:255'],
            'organization_id' => ['required', 'integer', Rule::exists('unity_organizations', 'id')],
        ];
    }
}
