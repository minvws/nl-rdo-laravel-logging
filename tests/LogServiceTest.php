<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests;

use MinVWS\AuditLogger\Events\Logging\UserLoginLogEvent;
use MinVWS\AuditLogger\Loggers\LoggerInterface;
use MinVWS\Logging\Laravel\LogService;
use Mockery;

class LogServiceTest extends Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testContructedLoggers(): void
    {
        $mockService = Mockery::mock(LoggerInterface::class);
        $mockService->shouldReceive('canHandleEvent')->once()->andReturn(false);

        $mockService2 = Mockery::mock(LoggerInterface::class);
        $mockService2->shouldReceive('canHandleEvent')->once()->andReturn(false);

        $service = new LogService([$mockService, $mockService2]);
        $service->log(new UserLoginLogEvent());
    }

    public function testAddedLoggers(): void
    {
        $mockService = Mockery::mock(LoggerInterface::class);
        $mockService->shouldReceive('canHandleEvent')->once()->andReturn(false);

        $service = new LogService([]);
        $service->addLogger($mockService);
        $service->log(new UserLoginLogEvent());
    }

    public function testLogging(): void
    {
        $mockService = Mockery::mock(LoggerInterface::class);
        $mockService->shouldReceive('canHandleEvent')->once()->andReturn(true);
        $mockService->shouldReceive('log')->once();

        $service = new LogService([$mockService]);
        $service->log(new UserLoginLogEvent());
    }
}
