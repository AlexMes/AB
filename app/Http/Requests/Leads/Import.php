<?php

namespace App\Http\Requests\Leads;

use App\Lead;
use Illuminate\Foundation\Http\FormRequest;

class Import extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('import', Lead::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'external_id'     => 'required|string|max:255',
            'status'          => 'sometimes|string|max:255',
            'responsible'     => 'sometimes|string|max:255',
            'comment'         => 'sometimes|string|max:255',
            'office_id'       => 'sometimes|string|max:255',
            'department_id'   => 'sometimes|string|max:255',
            'called_at'       => 'sometimes|string|max:255',
        ];
    }
}
