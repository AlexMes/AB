<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CensorRusRule implements Rule
{
    public function passes($attribute, $value)
    {
        return ObsceneCensorRus::isAllowed($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Ненормативная лексика.';
    }
}
