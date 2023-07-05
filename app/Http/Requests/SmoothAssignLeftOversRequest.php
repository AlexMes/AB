<?php

namespace App\Http\Requests;

use App\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class SmoothAssignLeftOversRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return in_array($this->user()->branch_id, [19, 16, 20, 26, 28]) && $this->user()->can('assignLeftovers', Lead::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount'        => ['required','int','min:1'],
            'deliver_since' => [
                'nullable',
                'date_format:Y-m-d H:i',
                'after_or_equal:' . now()->addMinutes(5)->format('Y-m-d H:i'),
                'before_or_equal:' . Carbon::parse($this->input('deliver_until'))->subMinutes(5)->format('Y-m-d H:i'),
            ],
            'deliver_until' => [
                'required',
                'date_format:Y-m-d H:i',
                'after_or_equal:' . now()->addMinutes(30)->format('Y-m-d H:i'),
            ],
            'sort_order'    => ['sometimes', Rule::in(['asc', 'desc'])],
            'destination'   => ['sometimes', 'nullable', Rule::exists('lead_destinations', 'id')],
        ];
    }
}
