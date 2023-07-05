<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('order'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'links_count'        => ['required','integer','min:1'],
            'cloak_id'           => ['nullable','int','exists:cloaks,id'],
            'binom_id'           => ['required','integer','min:0','exists:binom_campaigns,campaign_id'],
            'linkType'           => ['required','string','min:3','max:40',],
            'registrar_id'       => ['required','int','exists:registrars,id',],
            'useCloudflare'      => 'boolean',
            'useConstructor'     => 'boolean',
            'landing_id'         => ['nullable','int','exists:domains,id'],
            'offer_id'           => ['nullable','int','exists:offers,id'],
            'sp_id'              => ['nullable','int','exists:pages,id'],
            'bp_id'              => ['nullable','int','exists:pages,id'],
            'deadline_at'        => 'nullable','date','after_or_equal:now',
            'hosting_id'         => ['nullable','int','exists:hostings,id'],

        ];
    }
}
