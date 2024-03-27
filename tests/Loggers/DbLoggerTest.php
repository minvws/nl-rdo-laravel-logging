<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests\Loggers;

use Illuminate\Database\Eloquent\Model;
use MinVWS\Logging\Laravel\Events\Logging\UserLoginLogEvent;
use MinVWS\Logging\Laravel\Loggers\DbLogger;
use MinVWS\Logging\Laravel\Loggers\ModelFactoryInterface;
use MinVWS\Logging\Laravel\Tests\AuditLog;
use Orchestra\Testbench\TestCase;
use MinVWS\Logging\Laravel\Tests\User;

class DbLoggerTest extends TestCase implements ModelFactoryInterface
{
    public function testDblogger(): void
    {
        $user = new User();
        $user->email = "john@example.org";
        $user->id = '12345';

        $event = (new UserLoginLogEvent())
            ->withActor($user)
            ->withData(['foo' => 'bar'])
            ->withPiiData(['bar' => 'baz']);

        $service = new DbLogger(
            false,
            '',
            '',
            AuditLog::class,
            $this
        );
        $service->log($event);
    }

    public function create(string $modelFqcn, array $data): Model
    {
        /** @var Mockery\MockInterface|Model $mock */
        $mock = Mockery::mock($modelFqcn);
        $mock->shouldReceive('create')->with($data)->once();

        return $mock;
    }
}
