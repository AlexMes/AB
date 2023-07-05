<?php

namespace App\VK\Exceptions;

class Unauthorized extends VKException
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(string $message = 'VK unauthorized.', int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
