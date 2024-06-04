<?php

namespace MinVWS\Logging\Laravel\Loggers;

use Illuminate\Database\Eloquent\Model;

/**
 * A ModelFactory can be used to create a custom model for the DbLogger. It purpose is mostly for unit testing
 */
interface ModelFactoryInterface
{
    /**
     * @phpstan-param array<string, mixed> $data
     */
    public function create(string $modelFqcn, array $data): Model;
}
