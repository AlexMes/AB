<?php

namespace App\Http\Requests;

use App\GoogleApp;
use Illuminate\Foundation\Http\FormRequest;

class CreateGoogleApp extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', GoogleApp::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => ['required','unique:google_apps,name','min:3','max:50'],
            'market_id' => ['required','unique:google_apps,market_id','min:3','max:100'],
            'enabled'   => ['sometimes','boolean'],
            'url'       => ['required','url','max:255']
        ];
    }
}
