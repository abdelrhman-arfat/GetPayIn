<?php

namespace App\Exceptions;

use Exception;

class DuplicateEntry extends Exception
{
    public function __construct(string $message = 'Duplicate Entry', int $code = 409)
    {
        parent::__construct($message, $code);
    }
}
