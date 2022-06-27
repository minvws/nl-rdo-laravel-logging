<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Loggers;

use Illuminate\Database\Eloquent\Model;
use JsonException;

class DbLogger implements LoggerInterface
{
    // The eloquent model that will be created
    protected string $modelFqcn;

    public function __construct(string $modelFqcn)
    {
        $this->modelFqcn = $modelFqcn;
    }

    public function log(LogEventInterface $event): void
    {
        $data = $event->getMergedPiiData();
        if (isset($data['request'])) {
            try {
                $data['request'] = json_encode($data['request'], JSON_THROW_ON_ERROR);
            } catch (JsonException) {
            }
        }

        // Create a database record based for the configured model
        $model = new $this->modelFqcn();

        /** @var Model $model */
        // @phpstan-ignore-next-line
        $model::create($data);
    }

    public function canHandleEvent(LogEventInterface $event): bool
    {
        return true;
    }
}
