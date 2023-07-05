<?php

namespace App\Http\Requests\Affiliates;

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
            'name'              => ['required', 'string', 'max:255'],
            'offer_id'          => ['nullable', 'exists:offers,id'],
            'traffic_source_id' => ['nullable','exists:traffic_sources,id'],
            'branch_id'         => ['nullable', 'integer', Rule::exists('branches', 'id')],
            /*'cpl'               => ['numeric', 'min:0'],
            'cpa'               => ['numeric', 'min:0'],*/
            'postback'          => ['sometimes', 'string','active_url'],
        ];
    }
}
