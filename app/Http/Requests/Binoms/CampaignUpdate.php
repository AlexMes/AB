<?php

namespace App\Http\Requests\Binoms;

use Illuminate\Foundation\Http\FormRequest;

class CampaignUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('campaign'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'offer_id'  => ['numeric', 'nullable', 'exists:offers,id'],
        ];
    }
}
