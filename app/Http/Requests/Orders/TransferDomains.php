<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class TransferDomains extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id'     => ['required', 'exists:orders,id'],
            'domain_ids'   => ['required', 'array'],
            'domain_ids.*' => ['required', 'exists:domains,id'],
        ];
    }
}
