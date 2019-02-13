<?php

namespace App\Http\Responses;

use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Catch accidental requests from (javascript) clients such as /company/<undefined>/user/33
 * where <undefined> should have been an an integer.
 *
 */
class ParamConverterFailure
{
    public function onKernelException(GetResponseForExceptionEvent $event): void {
        $exception = $event->getException();

        if ($exception instanceof \Doctrine\DBAL\Exception\DriverException && '22P02' === $exception->getSQLState()) {
            /* PLEASE KEEP ME
             * if (false === strpos(getenv('APP_ENV'), 'prod')) {
             *     // error_log($this->jTraceEx($exception));
             *     // error_log(getPrettyTrace($exception->getTrace()));
             * }
             */
            $event->setResponse(JsonResponse::create([
                'errors' => [
                    [
                        'code'  => 'postgres:22P02',
                        'title' => 'Unexpeted value. Are you passing an invalid (id) parameter?'
                    ]
                ]
            ], Response::HTTP_BAD_REQUEST));
        }
    }

    private function isPreProd(): bool
    {
        return false === strpos(getenv('APP_ENV'), 'prod');
    }
}