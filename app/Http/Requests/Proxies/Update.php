<?php

namespace App\Http\Requests\Proxies;

use App\Proxy;
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
            'name'      => ['required', 'string', 'max:255'],
            'protocol'  => ['required', 'string', Rule::in(Proxy::PROTOCOLS)],
            'host'      => ['required', 'string', 'ip', 'max:255'],
            'port'      => ['required', 'numeric', 'between:1,65536'],
            'login'     => ['nullable', 'string', 'max:255'],
            'password'  => ['nullable', 'string', 'max:255'],
            'geo'       => ['required', 'string', 'min:2', 'max:2'],
            'branch_id' => ['nullable', 'integer', Rule::exists('branches', 'id')],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
