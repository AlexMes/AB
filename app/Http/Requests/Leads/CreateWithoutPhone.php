<?php

namespace App\Http\Requests\Leads;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Create
 *
 * @property-read  string|null $domain
 * @property-read  string|null $firstname
 * @property-read  string|null $lastname
 * @property-read  string|null $email
 * @property-read  string|null $phone
 * @property-read  string|null $ip
 * @property-read  string|null $form_type
 * @property-read  string|null $utm_source
 * @property-read  string|null $utm_content
 * @property-read  string|null $utm_campaign
 * @property-read  string|null $utm_term
 * @property-read  string|null $utm_medium
 * @property-read  string|null $clickid
 *
 * @package App\Http\Requests\Leads
 */
class CreateWithoutPhone extends FormRequest
{
    /**
     * Validation rules
     *
     * @return array
     */
    public function rules()
    {
        return[
            'domain'                    => ['string', 'nullable', 'max:150'],
            'firstname'                 => ['string', 'required','min:2', 'max:50'],
            'lastname'                  => ['string', 'required', 'min:2', 'max:50'],
            'middlename'                => ['string', 'nullable', 'max:50'],
            'email'                     => ['string', 'required', 'max:50'],
            'phone'                     => ['string', 'nullable', 'max:255'],
            'ip'                        => ['string', 'nullable', 'max:255'],
            'form_type'                 => ['string', 'nullable', 'max:255'],
            'utm_source'                => ['string', 'nullable', 'max:255'],
            'utm_content'               => ['string', 'nullable', 'max:255'],
            'utm_campaign'              => ['string', 'nullable', 'max:255'],
            'utm_term'                  => ['string', 'nullable', 'max:255'],
            'utm_medium'                => ['string', 'nullable', 'max:255'],
            'clickid'                   => ['string', 'nullable', 'max:255'],
            'geetest_challenge'         => ['string', 'nullable', 'max:255'],
            'geetest_validate'          => ['string', 'nullable', 'max:255'],
            'geetest_seccode'           => ['string', 'nullable', 'max:255'],
            'password'                  => ['string', 'nullable', 'max:255'],
            'created_at'                => ['nullable','string']
        ];
    }
}
