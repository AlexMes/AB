<?php

namespace App\Http\Requests\Users\Firewall;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Create extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ip'         => [
                'required','ip',Rule::unique('firewall_rules')->where('user_id', $this->route('user')->id)],
        ];
    }
}
