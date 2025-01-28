<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CoachNotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(404, "Entrenador no encontrado");
    }
}