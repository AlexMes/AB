<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeLeadOrderOffer extends FormRequest
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
            || $this->user()->isDeveloper()
            || $this->user()->isSubSupport()
            || $this->user()->isBranchHead() && $this->user()->can('update', $this->route('order'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from_offer_id'  => [
                'required',
                'exists:offers,id',
            ],
            'to_offer_id'  => [
                'required',
                'exists:offers,id',
            ],
        ];
    }
}
