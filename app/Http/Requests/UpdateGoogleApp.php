<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGoogleApp extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('app'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => ['required',Rule::unique('google_apps')->ignoreModel($this->route('app')),'min:3','max:50'],
            'market_id' => ['required',Rule::unique('google_apps')->ignoreModel($this->route('app')),'min:3','max:100'],
            'enabled'   => ['sometimes','boolean'],
            'url'       => ['required','url','max:255']
        ];
    }
}
