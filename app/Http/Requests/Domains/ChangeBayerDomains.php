<?php

namespace App\Http\Requests\Domains;

use Illuminate\Foundation\Http\FormRequest;

class ChangeBayerDomains extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin() || $this->user()->isTeamLead();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'      => ['required', 'exists:users,id'],
            'domain_ids'   => ['required', 'array'],
            'domain_ids.*' => ['required', 'exists:domains,id'],
        ];
    }
}
