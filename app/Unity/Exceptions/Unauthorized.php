<?php

namespace App\Unity\Exceptions;

class Unauthorized extends UnityException
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(string $message = 'Unity unauthorized.', int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
