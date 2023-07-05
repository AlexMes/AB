<?php

namespace App\Http\Requests\Results;

use Illuminate\Foundation\Http\FormRequest;

class CreateResultRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date'                                => ['required','date'],
            'offer_id'                            => ['required','integer', 'exists:offers,id'],
            'office_id'                           => ['required','integer', 'exists:offices,id'],
            'leads_count'                         => ['required'],
            'no_answer_count'                     => ['nullable'],
            'reject_count'                        => ['nullable'],
            'wrong_answer_count'                  => ['nullable'],
            'demo_count'                          => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'The :attribute field is required.',
        ];
    }
}
