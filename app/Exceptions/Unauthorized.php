<?php

namespace App\Exceptions;

use Exception;

class Unauthorized extends Exception
{
    public function __construct(string $message = 'Login first and try again ', int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
