<?php

namespace App\Deluge\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDomain extends FormRequest
{
    /**
     * Access check
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('domain'));
    }

    /**
     * Validation rules for request
     *
     * @return array
     */
    public function rules()
    {
        return [
            'url'         => ['string',Rule::unique('domains', 'url')->ignoreModel($this->route('domain'))],
            'user_id'     => ['required','int','exists:users,id'],
            'destination' => ['nullable','string']
        ];
    }
}
