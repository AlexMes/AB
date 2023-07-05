<?php

namespace App\Facebook\Exceptions;

class FacebookClientNotDefinedException extends \Exception
{
    /**
     * FacebookClientNotDefinedException constructor.
     */
    public function __construct()
    {
        parent::__construct('Facebook http client not defined');
    }
}
