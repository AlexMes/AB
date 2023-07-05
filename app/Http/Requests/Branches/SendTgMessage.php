<?php

namespace App\Http\Requests\Branches;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class SendTgMessage extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('view', $this->route('branch'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        throw_unless(
            $this->route('branch')->telegram_id,
            ValidationException::withMessages(['branch' => 'Telegram ID is not set on the branch.'])
        );

        return [
            'message' => ['required', 'string'],
        ];
    }
}
