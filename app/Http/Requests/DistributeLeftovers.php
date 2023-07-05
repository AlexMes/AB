<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DistributeLeftovers extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin() && $this->user()->id !== 230
            || $this->user()->isSupport()
            || $this->user()->isBranchHead();
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
