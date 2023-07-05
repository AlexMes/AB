<?php

namespace App\Http\Requests;

use App\LeadOrderRoute;
use Illuminate\Foundation\Http\FormRequest;

class CreateLeadsOrderRoute extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', LeadOrderRoute::class);
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
