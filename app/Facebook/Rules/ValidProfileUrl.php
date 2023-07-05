<?php

namespace App\Facebook\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidProfileUrl implements Rule
{
    /**
     * Regex patter to check url
     *
     * @var string
     */
    protected $pattern = '/(?:http:\/\/)?(?:www\.)?facebook\.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-]*\/)*([\w\-]*)/';

    /**
     * Check is certain attribute pass validation
     *
     * @param string $attribute
     * @param string $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match_all($this->pattern, $value);
    }

    /**
     * Get validation message text
     *
     * @return string
     */
    public function message()
    {
        return 'Ссылка на профиль некорректна';
    }
}
