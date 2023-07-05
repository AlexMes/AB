<?php

namespace App\Http\Requests\Deposits;

use App\Deposit;
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
        return $this->user()->can('create', Deposit::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lead_return_date' => ['required', 'date'],
            'date'             => ['required', 'date'],
            'sum'              => ['nullable', 'numeric'],
            'phone'            => ['required', 'numeric'],
            // 'account_id'       => ['nullable', 'exists:facebook_ads_accounts,account_id'],
            'user_id'          => ['required', 'exists:users,id'],
            'office_id'        => ['required', 'exists:offices,id'],
            'offer_id'         => ['required', 'exists:offers,id'],
            'lead_id'          => ['nullable', 'exists:leads,id'],
        ];
    }
}
