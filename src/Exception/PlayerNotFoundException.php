<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class PlayerNotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(404, "Jugador no encontrado");
    }
}