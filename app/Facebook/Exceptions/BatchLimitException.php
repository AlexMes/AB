<?php

namespace App\Facebook\Exceptions;

class BatchLimitException extends \Exception
{
    const BATCH_LIMIT = 50;

    public function __construct()
    {
        parent::__construct(sprintf('Cant make more than %s queries in one batch request', self::BATCH_LIMIT));
    }
}
