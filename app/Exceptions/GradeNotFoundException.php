<?php

namespace App\Exceptions;

use Exception;

class GradeNotFoundException extends Exception
{
    public function __construct(string $message = "")
    {
        parent::__construct($message);
    }
}
