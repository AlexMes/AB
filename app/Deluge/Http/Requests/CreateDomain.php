<?php

namespace App\Deluge\Http\Requests;

use App\Deluge\Domain;
use Illuminate\Foundation\Http\FormRequest;

class CreateDomain extends FormRequest
{
    /**
     * Authorize action
     *
     * @return void
     */
    public function authorize()
    {
        return $this->user()->can('create', Domain::class);
    }

    /**
     * Validation rules
     *
     * @return void
     */
    public function rules()
    {
        return [
            'url'         => ['string'],
            'user_id'     => ['required','int','exists:users,id'],
            'destination' => ['nullable','string']
        ];
    }
}
