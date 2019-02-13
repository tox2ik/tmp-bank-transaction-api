<?php

namespace App;

use Doctrine\DBAL\Driver\DriverException;
//use
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**


 * todo: do not set the responses here.
 * move to http/responses
 */
class ExceptionLogger
{
    public static function error_log($ex)
    {
        error_log((new static())->jTraceEx($ex));
        //throw $ex;
    }

    /**
     * log the error in addition to dumping it as HTML (in dev)
     * todo: verify secure production
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $ex = $exception;
        $exHistory = get_class($exception) . ':' .  $exception->getMessage();

        /// while ($prev = $ex->getPrevious()) {
        ///     $ex = $prev;
        ///     $exHistory  = "$exHistory\n" . get_class($exception) . ':' . $prev->getMessage();
        /// }

        // error_log($exHistory);


        //$event->setResponse(new JsonResponse(['errors' => ['Unhandled exception']], 500));

        /// if ($exception instanceof DriverException) {
        ///     /// $stack = $exception->getTrace();
        ///     /// foreach ($stack as $e) {
        ///     ///     if (isset($e['class']) && $e['class'] == DoctrineParamConverter::class) {
        ///     ///         error_log(sprintf("%s:%s\n%s", get_class($exception), $exception->getMessage(), getPrettyTrace($stack)));
        ///     ///         break;
        ///     ///     }
        ///     /// }
        ///     error_log($this->jTraceEx($exception));
        ///     return $event->setResponse(JsonResponse::create([ 'errors' => [
        ///         'Invalid query',
        ///         $exception->getMessage()
        ///     ] ], Response::HTTP_BAD_REQUEST));
        /// }

        if ($exception instanceof  MethodNotAllowedHttpException) {
            return;
        }


        if ($exception instanceof AccessDeniedHttpException) {
            $event->setResponse(Response::create(null, Response::HTTP_UNAUTHORIZED));
            return;
        }


        if ($exception instanceof InsufficientAuthenticationException) {
            $event->setResponse(Response::create(null, Response::HTTP_FORBIDDEN));
            return;
        }

        if ($exception instanceof NotFoundHttpException) {
            $event->setResponse(
                JsonResponse::create(
                    [ 'errors' => [ $exception->getMessage() ] ],
                    Response::HTTP_BAD_REQUEST
                )
            );
            return;
        }


        if ($exception instanceof HttpException) {
            $event->setResponse(
                JsonResponse::create(
                    [ 'errors' => [ $exception->getMessage() ] ],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                )
            );
            return;
        }





        if ($exception instanceof \Doctrine\DBAL\Exception\DriverException && '22P02' === $exception->getSQLState() ) {
            // catch requests like /api/v1/company/undefined/users. undefined should be an integer.

            error_log($this->jTraceEx($exception));
            error_log(getPrettyTrace($exception->getTrace()));

            $event->setResponse(
                JsonResponse::create(
                    [ 'errors' => [[
                        'code' => 'postgres:22P02',
                        'title' => 'Unexpeted value. Are you passing an invalid (id) parameter?']] ],
                    Response::HTTP_BAD_REQUEST
                ));
            return;
        }

        if ($exception instanceof BadCredentialsException) {

            $event->setResponse(
                JsonResponse::create(
                    [ 'errors' => [[
                        'id' => 'missing_auth_info',
                        'title' => 'Please provide an X-Simple-Key header.']] ],
                    Response::HTTP_BAD_REQUEST
                ));
            return;

        }



        if ($exception instanceof CustomUserMessageAuthenticationException) {
            $event->setResponse(
                JsonResponse::create(
                    [ 'errors' => [ $exception->getMessage() ]],
                    Response::HTTP_BAD_REQUEST
                ));
            return;

        }

        // if AuthenticationCredentialsNotFoundException

        /**
        if ($exception instanceof \Doctrine\DBAL\Exception\DriverException) {
            do {
            error_log('DRIVER ERROR STACK ' . $exception->getMessage());
            $lastCause = $cause;
            $cause = $exception->getPrevious();
            } while ($cause && $lastCause !== $cause);

        }
        **/

        error_log($this->jTraceEx($exception));
        error_log(getPrettyTrace($exception->getTrace()));
    }

    /**
     * jTraceEx() - provide a Java style exception trace
     * @param $e
     * @param $seen      - array passed to recursive calls to accumulate trace lines already seen
     *                     leave as NULL when calling this function
     * @return string
     */
    private function jTraceEx($e, $seen = null)
    {
        $starter = $seen ? 'Caused by: ' : '';
        $result = array();
        if (!$seen) $seen = array();
        $trace  = $e->getTrace();
        $prev   = $e->getPrevious();
        $result[] = sprintf('%s%s: %s', $starter, get_class($e), $e->getMessage());
        $file = $e->getFile();
        $line = $e->getLine();
        while (true) {
            $current = "$file:$line";
            if (is_array($seen) && in_array($current, $seen)) {
                $result[] = sprintf(' ... %d more', count($trace)+1);
                break;
            }
            $result[] = sprintf(' at %s%s%s(%s%s%s)',
                count($trace) && array_key_exists('class', $trace[0]) ? str_replace('\\', '.', $trace[0]['class']) : '',
                count($trace) && array_key_exists('class', $trace[0]) && array_key_exists('function', $trace[0]) ? '.' : '',
                count($trace) && array_key_exists('function', $trace[0]) ? str_replace('\\', '.', $trace[0]['function']) : '(main)',
                $line === null ? $file : basename($file),
                $line === null ? '' : ':',
                $line === null ? '' : $line);
            if (is_array($seen))
                $seen[] = "$file:$line";
            if (!count($trace))
                break;
            $file = array_key_exists('file', $trace[0]) ? $trace[0]['file'] : 'Unknown Source';
            $line = array_key_exists('file', $trace[0]) && array_key_exists('line', $trace[0]) && $trace[0]['line'] ? $trace[0]['line'] : null;
            array_shift($trace);
        }
        $result = join("\n", $result);
        if ($prev)
            $result  .= "\n" . $this->jTraceEx($prev, $seen);

        return $result;
    }

}

