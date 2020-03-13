<?php

namespace App\Util\Exception;

class UnauthorizedException extends AppException
{
    public function __construct($message = 'Acceso no autorizado')
    {
        parent::__construct($message, 403);
    }
}
