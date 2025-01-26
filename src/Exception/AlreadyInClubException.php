<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AlreadyInClubException extends HttpException
{
    public function __construct(string $message = 'El jugador/entrenador ya pertenece a un club')
    {
        parent::__construct(409, $message); // 409 Conflict
    }
}