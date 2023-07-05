<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PackColdBase extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin() || $this->user()->isSupport() && $this->user()->branch_id === 19;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'since'  => ['required', 'date_format:Y-m-d'],
            'until'  => ['required', 'date_format:Y-m-d'],
            'offer'  => ['required', 'integer', Rule::exists('offers', 'id')],
            'amount' => ['required', 'integer', 'min:1'],
        ];
    }
}
