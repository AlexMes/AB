<?php

namespace App\Http\Requests\ResellBatches;

use App\ResellBatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class Update extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var ResellBatch $batch */
        $batch = $this->route('resell_batch');
        throw_if(
            $batch->status !== 'pending',
            ValidationException::withMessages(['modification_forbidden' => 'Batch cannot be modified after start.'])
        );

        return [
            'name'                 => ['required', 'string', 'max:255'],
            'registered_at'        => ['required', 'date_format:Y-m-d', 'before_or_equal:now'],
            'offices'              => ['nullable', 'array'],
            'offices.*'            => ['integer', Rule::exists('offices', 'id')],
            'substitute_offer'     => ['nullable'],
            'create_offer'         => ['sometimes', 'boolean'],
            'simulate_autologin'   => ['sometimes', 'boolean'],
            'ignore_paused_routes' => ['sometimes', 'boolean'],
        ];
    }
}
