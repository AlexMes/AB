<?php

namespace App\CRM\Http\Requests;

use App\CRM\Age;
use App\CRM\LeadOrderAssignment;
use App\CRM\Profession;
use App\CRM\Reason;
use App\CRM\Status;
use App\CRM\Timezone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateAssignment extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('assignment'));
    }

    /**
     * @return array
     */
    public function rules()
    {
        /** @var LeadOrderAssignment $assignment */
        $assignment = $this->route('assignment');
        throw_if(
            $assignment->status === 'Депозит' && !auth('web')->check(),
            ValidationException::withMessages(['status_deposit_blocked' => 1])
        );

        return [
            'status'        => ['required','string', Rule::in(Status::selectable()->pluck('name'))],
            'reject_reason' => ['nullable',
                Rule::requiredIf(fn () => $this->input('status') === 'Отказ'), Rule::in(Reason::pluck('name')->all())],
            'deposit_sum' => ['nullable',
                Rule::requiredIf(fn () => $this->input('status') === 'Депозит'), 'numeric','max:100000','min:50'],
            'gender_id' => [
                'sometimes',
                'int',
                Rule::requiredIf(fn () => $this->canNotSkipLeadDetails()),
            ],
            'age' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in(Age::pluck('name')->all()),
                Rule::requiredIf(fn () => $this->canNotSkipLeadDetails()),
            ],
            'profession' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in(Profession::pluck('name')->all()),
                Rule::requiredIf(fn () => $this->canNotSkipLeadDetails()),
            ],
            'comment'     => ['sometimes', 'nullable','string','max:1024'],
            'alt_phone'   => ['sometimes', 'nullable','numeric','digits_between:10,15'],
            'callback_at' => [
                'nullable',
                'exclude_unless:status,Перезвон',
                'date_format:Y-m-d H:i',
                'after:' . now()->toDateTimeString(),
                Rule::requiredIf(fn () => $this->input('status') === 'Перезвон'),
            ],
            'timezone' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in(Timezone::pluck('name')->all()),
            ],
        ];
    }

    /**
     * Determines when we can skip lead details
     *
     * @return bool
     */
    protected function canNotSkipLeadDetails(): bool
    {
        if ($this->input('status') === 'Отказ') {
            return $this->input('reject_reason') !== 'сброс';
        }

        return !in_array($this->input('status'), Status::PASSTHROUGHS_VALIDATION);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return Arr::except(trans('crm::validation'), ['attributes', 'custom']);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('crm::validation.attributes');
    }
}
