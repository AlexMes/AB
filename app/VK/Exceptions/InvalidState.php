<?php

namespace App\VK\Exceptions;

class InvalidState extends VKException
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(string $message = 'Invalid state.', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
