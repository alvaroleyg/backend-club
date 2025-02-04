<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AlreadyInClubException extends HttpException
{
    public function __construct()
    {
        parent::__construct(409, "El jugador/entrenador ya pertenece a un club");
    }
}