<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MakeLeadUnpayable extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->branch_id === 16 && $this->user()->can('update', $this->route('lead'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
