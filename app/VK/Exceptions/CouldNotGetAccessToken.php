<?php

namespace App\VK\Exceptions;

class CouldNotGetAccessToken extends VKException
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(string $message = 'Access token was not returned from API.', int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
