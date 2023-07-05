<?php

namespace App\Binom\Exceptions;

use Exception;

class BinomReponseException extends Exception
{
    /**
     * @throws \App\Binom\Exceptions\BinomReponseException
     */
    public static function clickIsMissing()
    {
        throw new static('Click info missing from response.');
    }
}
