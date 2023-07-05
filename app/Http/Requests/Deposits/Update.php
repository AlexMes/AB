<?php

namespace App\Http\Requests\Deposits;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('deposit'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lead_return_date' => ['nullable', 'date'],
            'date'             => ['nullable', 'date'],
            'sum'              => ['nullable', 'numeric'],
            'phone'            => ['nullable', 'string'],
            'account_id'       => ['nullable'],
            'user_id'          => ['nullable', 'exists:users,id'],
            'office_id'        => ['nullable', 'exists:offices,id'],
            'offer_id'         => ['nullable', 'exists:offers,id'],
            'lead_id'          => ['nullable', 'exists:leads,id'],
        ];
    }
}
