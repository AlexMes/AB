<?php

namespace App\Http\Requests\BinomTrafficSources;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrafficSource extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'traffic_source_id' => ['nullable', 'exists:traffic_sources,id'],
        ];
    }
}
