<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Tell the user their requested endpoint is not defined.
 *
 * When a route does not exist or a route exists but the provided document-id does not exist;
 *
 *     - Respond with 400 or 410. Not 404.
 *
 * Because:
 *
 *     - HTTP 400 means _you don't know what you are talking about_ or _I don't know what you mean_
 *         - i.e. route exists but your id is wrong
 *     - HTTP 404 is cacheable, and we may provide the requested URL later.
 *         - e.g. /posts/24
 *     - HTTP 404 means _I don't have it_ or _I don't want you to know that I have it_
 *         - The developer of the front-end application is likely to associate 404 with a typo in the URL,
 *           and /api/v1/valid-resource/<invalid-id> is not a typo.
 *
 * This interpretation is based on:
 *
 * - rfc 7231 [https://tools.ietf.org/html/rfc7231#section-6.5.1](http 400)
 *
 * - rfc 7231 [https://tools.ietf.org/html/rfc7231#section-6.5.4](http 404)
 *
 *
 */
class NoSuchRoute
{
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();
        if ($exception instanceof MethodNotAllowedHttpException) {
            $event->setResponse(JsonResponse::create([
                'errors' => [
                    [
                        'id'    => 'no_such_route',
                        'title' => 'The requested endpoint and or http-method is invalid',
                        'detail' => str_replace('"', '', $exception->getMessage())
                    ]
                ]
            ], Response::HTTP_BAD_REQUEST));
        }
    }
}