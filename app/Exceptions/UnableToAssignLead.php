<?php

namespace App\Exceptions;

use Exception;

class UnableToAssignLead extends Exception
{
    public static function routeLocked()
    {
        throw new static('Route lock cannot be obtained');
    }
}
