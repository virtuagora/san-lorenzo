<?php

namespace App\Util\Exception;

use RuntimeException;

class SystemException extends RuntimeException
{
    public function __construct($message, $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
