<?php

namespace App\Http\Requests\Leads;

use App\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class Delete extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('delete', $this->route('lead'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var Lead $lead */
        $lead = $this->route('lead');
        throw_if(
            $lead->assignments()->count() > 0,
            ValidationException::withMessages(['has_assignments' => 'Lead with assignments cannot be deleted.'])
        );

        return [
            //
        ];
    }
}
