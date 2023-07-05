<?php

namespace App\Http\Requests;

use App\LeadsOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateLeadsOrder extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', LeadsOrder::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date'                  => ['date_format:Y-m-d','after_or_equal:' . now()->toDateString()],
            'office_id'             => ['required','exists:offices,id'],
            'destination_id'        => ['nullable','int','exists:lead_destinations,id'],
            'start_at'              => ['nullable', 'date_format:H:i'],
            'stop_at'               => ['nullable', 'date_format:H:i'],
            'branch_id'             => [Rule::requiredIf(fn () => auth()->user()->isAdmin()), 'nullable', Rule::exists('branches', 'id')],
            'autodelete_duplicates' => ['sometimes', 'boolean'],
            'deny_live'             => ['sometimes', 'boolean'],
            'live_interval'         => ['sometimes', 'required', 'integer', 'min:0', 'max:1800'],
        ];
    }

    protected function prepareForValidation()
    {
        if (! auth()->user()->isAdmin()) {
            $this->merge([
                'branch_id' => auth()->user()->branch_id,
            ]);
        }
    }
}
