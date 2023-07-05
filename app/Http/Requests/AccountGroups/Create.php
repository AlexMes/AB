<?php

namespace App\Http\Requests\AccountGroups;

use App\AccountGroup;
use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('store', AccountGroup::class);
    }

    public function rules()
    {
        return [
            'name'     => ['required','string','min:2','max:30'],
            'offer_id' => ['required','int','exists:offers,id']
        ];
    }
}
