<?php

namespace App\FRX\Http\Requests;

use App\CRM\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssignment extends FormRequest
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
            'status' => ['sometimes', 'required', 'string', Rule::in(Status::pluck('name'))],
        ];
    }
}
