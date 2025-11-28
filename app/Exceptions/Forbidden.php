<?php

namespace App\Exceptions;

use Exception;

class Forbidden extends Exception
{
    public function __construct(string $message = "You don't have permission to do this action", int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
