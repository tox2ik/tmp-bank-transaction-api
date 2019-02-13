<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

class AuthorizationFailures
{
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        if ($exception instanceof AccessDeniedHttpException) {
            $event->setResponse(Response::create(null, Response::HTTP_UNAUTHORIZED));
        }


        if ($exception instanceof InsufficientAuthenticationException) {
            $event->setResponse(Response::create(null, Response::HTTP_FORBIDDEN));
        }


    }
}