<?php

namespace App\Http\Requests\Bundles;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('bundle'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'offer_id'      => 'nullable|integer|exists:offers,id',
            'utm_campaign'  => 'nullable|string|max:255',
            'examples'      => 'nullable|string',
            'geo'           => 'nullable|string|max:255',
            'age'           => 'nullable|integer|min:1|max:255',
            'gender'        => 'nullable|string|max:255',
            'interests'     => 'nullable|string|max:255',
            'device'        => 'nullable|string|max:255',
            'platform'      => 'nullable|string|max:255',
            'ad'            => 'nullable|string',
            'placements'    => 'array|exists:placements,id',
            'prelend_link'  => 'nullable|url|max:255',
            'lend_link'     => 'nullable|url|max:255',
            'utm_source'    => 'nullable|string|max:255',
            'utm_content'   => 'nullable|string|max:255',
            'utm_term'      => 'nullable|string|max:255',
            'utm_medium'    => 'nullable|string|max:255',
            'title'         => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:2048',
            'text'          => 'nullable|string',
        ];
    }
}
