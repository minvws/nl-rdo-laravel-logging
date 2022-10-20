<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Loggers;

use Illuminate\Database\Eloquent\Model;
use JsonException;

class DbLogger implements LoggerInterface
{
    // The eloquent model that will be created
    protected string $modelFqcn;

    // An additional factory that will generate the model for us
    protected ?ModelFactoryInterface $modelFactory;

    public function __construct(string $modelFqcn, ?ModelFactoryInterface $modelFactory = null)
    {
        $this->modelFqcn = $modelFqcn;
        $this->modelFactory = $modelFactory;
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

        // Create the model based on the FQCN or the factory
        if ($this->modelFactory) {
            $model = $this->modelFactory->create($this->modelFqcn, $data);
        } else {
            $model = new $this->modelFqcn();
        }

        /** @var Model $model */
        $model::create($data);      // @phpstan-ignore-line
    }

    public function canHandleEvent(LogEventInterface $event): bool
    {
        return true;
    }
}
