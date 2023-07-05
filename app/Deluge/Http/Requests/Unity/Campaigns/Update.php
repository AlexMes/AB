<?php

namespace App\Deluge\Http\Requests\Unity\Campaigns;

use App\UnityCampaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'      => ['required', 'string', Rule::unique('unity_campaigns')->ignore($this->route('campaign'))],
            'name'    => ['required', 'string', 'max:255'],
            'goal'    => ['required', 'string', Rule::in(UnityCampaign::GOALS)],
            'enabled' => ['required', 'boolean'],
            'app_id'  => ['required', 'string', Rule::exists('unity_apps', 'id')],
        ];
    }
}
