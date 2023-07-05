<?php

namespace App\Http\Requests\LeadsDestinations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CollectResults extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin() ||
            $this->user()->isSupport() && $this->user()->branch_id === $this->route('leads_destination')->branch_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        throw_unless(
            $this->route('leads_destination')->is_active,
            ValidationException::withMessages(['destination' => 'Destination should be active.'])
        );

        return [
            'since' => ['required', 'date_format:Y-m-d', 'before_or_equal:' . now()->toDateString()],
        ];
    }
}
