<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ClubNotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(404, "Club no encontrado");
    }
}