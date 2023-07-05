<?php

namespace App\Http\Requests\External;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountList extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'since'    => ['nullable', 'date'],
            'until'    => ['nullable', 'date'],
            'search'   => ['sometimes','string','max:50'],
            'orderBy'  => ['sometimes', 'string', Rule::in(['spend', 'lifetime'])],
            'dir'      => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ];
    }
}
