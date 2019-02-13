<?php

namespace App\Http\Responses;

use Monolog\Handler\GelfHandlerTest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ACHTUNG!
 *
 * Depending on your use cases for the HTTP system thesis response may not make sense.
 *
 * That is, you may want to respond with 404.
 *
 * Use the convert_not_found_exception_to_400 parameter to change this behavior.
 *
 */
class HttpNotFoundFailure
{
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();
        $convert_to_400 = getenv('convert_not_found_exception_to_400', true);
        if ($exception instanceof NotFoundHttpException) {

            if ($convert_to_400) {}
            $event->setResponse(
                JsonResponse::create(
                    [ 'errors' => [ $exception->getMessage() ] ],
                    $convert_to_400
                        ? Response::HTTP_BAD_REQUEST
                        : Response::HTTP_NOT_FOUND

                )
            );
        }
    }
}