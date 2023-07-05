<?php

namespace App\Deluge\Http\Requests\Unity\Insights;

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
            'date' => [
                'required',
                'date',
                'before_or_equal:now',
                Rule::unique('unity_insights', 'date')
                    ->where('app_id', $this->input('app_id'))
                    ->where('campaign_id', $this->input('campaign_id'))
                    ->ignore($this->route('insight')),
            ],
            'app_id'      => ['required', 'string', Rule::exists('unity_apps', 'id')],
            'campaign_id' => ['required', 'string', Rule::exists('unity_campaigns', 'id')],
            'views'       => ['required', 'integer', 'min:0'],
            'clicks'      => ['required', 'integer', 'min:0'],
            'spend'       => ['required', 'numeric', 'min:0'],
            'installs'    => ['required', 'integer', 'min:0'],
        ];
    }
}
