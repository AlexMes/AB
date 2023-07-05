<?php

namespace App\Http\Requests;

use App\TrafficSource;
use Illuminate\Foundation\Http\FormRequest;

class CreateTrafficSource extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', TrafficSource::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required','max:30','unique:traffic_sources,name']
        ];
    }
}
