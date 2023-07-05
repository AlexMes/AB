<?php

namespace App\Facebook\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidLogin implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false
            || preg_match('~^[0-9]{9,15}$~', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Логин должен быть e-mail или телефон(цифры от 0 до 9)';
    }
}
