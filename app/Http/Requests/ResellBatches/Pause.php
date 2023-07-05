<?php

namespace App\Http\Requests\ResellBatches;

use App\ResellBatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class Pause extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('resell_batch'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var ResellBatch $batch */
        $batch = $this->route('resell_batch');
        throw_unless(
            in_array($batch->status, [ResellBatch::IN_PROCESS]),
            ValidationException::withMessages(['modification_forbidden' => 'Batch cannot be paused in current state.'])
        );

        return [
            //
        ];
    }
}
