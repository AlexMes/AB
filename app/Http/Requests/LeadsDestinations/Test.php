<?php

namespace App\Http\Requests\LeadsDestinations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Test extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->id === 1 ||
            $this->user()->isSupport() && $this->user()->branch_id === $this->route('leads_destination')->branch_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'offer_id' => ['required', 'integer', Rule::exists('offers', 'id')],
            'geo'      => ['required', 'string', 'size:2', Rule::exists('countries', 'code')],
        ];
    }
}
