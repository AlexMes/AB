<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadsOrderRoute extends FormRequest
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
            || $this->user()->isBranchHead() && $this->user()->can('update', $this->route('route'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'manager_id'     => ['nullable','exists:managers,id'],
            'offer_id'       => ['required','exists:offers,id'],
            'leadsOrdered'   => ['required','numeric','min:1'],
            'destination_id' => ['nullable','int','exists:lead_destinations,id'],
            'start_at'       => ['nullable', 'date_format:H:i'],
            'stop_at'        => ['nullable', 'date_format:H:i'],
            'priority'       => ['required','boolean']
        ];
    }
}
