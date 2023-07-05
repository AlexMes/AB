<?php

namespace App\Http\Requests\Domains;

use App\Domain;
use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Domain::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'effectiveDate'       => ['string', 'nullable','date'],
            'url'                 => ['required', 'url'],
            'status'              => ['required', 'string'],
            'linkType'            => ['required','string'],
            'user_id'             => ['numeric', 'nullable', 'exists:users,id'],
            'sp_id'               => ['numeric', 'nullable', 'exists:pages,id'],
            'bp_id'               => ['numeric', 'nullable', 'exists:pages,id'],
            'cloak_id'            => ['numeric', 'nullable', 'exists:cloaks,id'],
            'order_id'            => ['numeric', 'nullable', 'exists:orders,id'],
            'offer_id'            => ['numeric', 'nullable', 'exists:offers,id'],
            'traffic_source_id'   => ['numeric', 'nullable', 'exists:traffic_sources,id'],
            'reach_status'        => ['sometimes', 'nullable', 'string'],
            'splitterEnabled'     => ['required', 'boolean'],
            'allow_duplicates'    => ['sometimes', 'nullable', 'boolean'],
        ];
    }
}
