<?php

namespace App\Http\Requests\Sms;

use App\SmsCampaign;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSmsCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', SmsCampaign::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'             => ['required','string'],
            'text'              => ['required','string'],
            'type'              => ['required','string',Rule::in([SmsCampaign::INSTANT,SmsCampaign::AFTER_TIME])],
            'after_minutes'     => ['nullable','int'],
            'landing_id'        => ['nullable','int','exists:domains,id'],
            'status'            => 'boolean',
            'branch_id'         => ['required', 'int', Rule::exists('branches', 'id')],
        ];
    }
}
