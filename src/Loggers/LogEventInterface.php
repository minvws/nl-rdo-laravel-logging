<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Loggers;

interface LogEventInterface
{
    // Get non-personal data to log
    public function getLogData(): array;

    // Get personal data to log
    public function getPiiLogData(): array;

    // Get merged array with non-personal and personal data
    public function getMergedPiiData(): array;

    // Returns the event key on which to log
    public function getEventKey(): string;
}
