<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOfficeMorningBranch extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasRole([User::ADMIN, User::SUPPORT]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'branch_id' => ['required', 'integer', Rule::exists('branches', 'id')],
            'time'      => ['required', 'date_format:H:i'],
            'duration'  => ['required', 'integer', 'min:1'],
        ];
    }

    protected function prepareForValidation()
    {
        $time = explode(':', $this->input('time'));
        if (count($time) === 2) {
            $seconds = (int)round((int)$time[1] / 10) * 10;

            $this->merge(['time' => $time[0] . ':' . ($seconds < 10 ? '0' : '') . $seconds]);
        }
    }
}
