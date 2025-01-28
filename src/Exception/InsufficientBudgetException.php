<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InsufficientBudgetException extends HttpException
{
    public function __construct(string $message = 'Presupuesto insuficiente')
    {
        parent::__construct(400, $message);
    }
}
