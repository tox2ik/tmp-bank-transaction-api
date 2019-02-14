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
 *
 */
class HttpNotFoundFailure
{
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();
        if ($exception instanceof NotFoundHttpException) {
            $event->setResponse(
                JsonResponse::create(
                    [ 'errors' => [ str_replace('"', '', $exception->getMessage()) ] ],
                    Response::HTTP_BAD_REQUEST
                )
            );
        }


        /// * Use the convert_not_found_exception_to_400 parameter to change this behavior.
        /// $exception = $event->getException();
        /// $convert_to_400 = getenv('convert_not_found_exception_to_400') === 0;
        /// $convert_to_400 = $convert_to_400 === null ? true : $convert_to_400;
        /// if ($exception instanceof NotFoundHttpException) {
        ///     if ($convert_to_400) {}
        ///     $event->setResponse(
        ///         JsonResponse::create(
        ///             [ 'errors' => [ $exception->getMessage() ] ],
        ///             $convert_to_400
        ///                 ? Response::HTTP_BAD_REQUEST
        ///                 : Response::HTTP_NOT_FOUND
        ///         )
        ///     );
        /// }
    }
}