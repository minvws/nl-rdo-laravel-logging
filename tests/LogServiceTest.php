<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests;

use MinVWS\Logging\Laravel\Events\Logging\UserLoginLogEvent;
use MinVWS\Logging\Laravel\Loggers\LoggerInterface;
use MinVWS\Logging\Laravel\LogService;
use Mockery;
use PHPUnit\Framework\TestCase;

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
