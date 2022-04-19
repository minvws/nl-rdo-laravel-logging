<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel;

use MinVWS\Logging\Laravel\Loggers\LogEventInterface;
use MinVWS\Logging\Laravel\Loggers\LoggerInterface;

class LogService
{
    /**
     * @param array<LoggerInterface> $loggers
     */
    public function __construct(protected array $loggers = [])
    {
    }

    public function addLogger(LoggerInterface $logger)
    {
        $this->loggers[] = $logger;
    }

    public function log(LogEventInterface $event): void
    {
        foreach ($this->loggers as $logger) {
            if (!$logger->canHandleEvent($event)) {
                continue;
            }

            $logger->log($event);
        }
    }
}
