<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class MarkLeadLeftover extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('markAsLeftover', $this->route('lead'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        throw_if(
            $this->route('lead')->offer->isLeftover(),
            ValidationException::withMessages(['lead' => 'Lead is already marked as leftover'])
        );

        return [
            //
        ];
    }
}
