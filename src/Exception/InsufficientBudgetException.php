<?php

// src/Exception/InsufficientBudgetException.php
namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InsufficientBudgetException extends HttpException
{
    public function __construct()
    {
        parent::__construct(400, "Presupuesto insuficiente");
    }
}