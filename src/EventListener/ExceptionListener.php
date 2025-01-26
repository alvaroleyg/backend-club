<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        
        $response = new JsonResponse(
            ['error' => $exception->getMessage()],
            $exception instanceof HttpException ? $exception->getStatusCode() : 500
        );
        
        $event->setResponse($response);
    }
}