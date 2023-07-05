<?php

namespace App\Http\Requests\Offers;

use App\Offer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('offer'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'               => ['required', 'string', 'min:3', 'max:40'],
            'vertical'           => ['nullable', 'string', 'max:255', Rule::in(Offer::VERTICALS)],
            'branch_id'          => ['required', 'integer', Rule::exists('branches', 'id')],
            'description'        => ['nullable', 'string'],
            'pb_lead'            => ['nullable','url'],
            'pb_sale'            => ['nullable','url'],
            'generate_email'     => ['nullable','boolean'],
            'allow_duplicates'   => ['nullable','boolean']
        ];
    }
}
