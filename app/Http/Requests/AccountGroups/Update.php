<?php

namespace App\Http\Requests\AccountGroups;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine is user authorized to make this request
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('accountGroup'));
    }

    /**
     * Validation rules for request
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => ['required','string','min:2','max:30'],
            'offer_id' => ['required','int','exists:offers,id']
        ];
    }
}
