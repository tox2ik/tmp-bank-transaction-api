<?php

namespace App\Http\Responses;

use Psr\Log\LoggerInterface;
use stdClass;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Prevent unhandled exceptions from reaching the client (aka pretend like we know like we are doing)
 *
 */
class LastExceptionHandler
{

    /**
     * @var LoggerInterface
     */
    public $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();
        $className = get_class($exception ?? new stdClass);  // get_class assumes $this if 1st arg is null
        $mess = $exception ? $exception->getMessage() : null;
        $type = basename(str_replace('\\', '/', $className));
        $this->logger->error(sprintf('Caught unforseen exception: %s: %s', $type, $mess));

        if ($this->isPreProd()) {
            error_log(sprintf('%s:%d %s', basename(__FILE__, '.php'), __LINE__, "$type: $mess"));
        }
    }

    private function isPreProd(): bool
    {
        return false === strpos(getenv('APP_ENV'), 'prod');
    }

}