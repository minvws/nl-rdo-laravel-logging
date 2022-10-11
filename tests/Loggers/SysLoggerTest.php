<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests\Loggers;

use Illuminate\Log\Logger;
use MinVWS\Logging\Laravel\Events\Logging\UserLoginLogEvent;
use MinVWS\Logging\Laravel\Loggers\SysLogger;
use Mockery;

class SysLoggerTest extends Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testSyslogger(): void
    {
        $mock = Mockery::mock(Logger::class);
        $mock->shouldReceive('info')->once()->withArgs(function ($args) {
            // Should return a json base64 encoded string
            return str_starts_with($args, 'AuditLog: eyJ1c2');
        });

        $event = new UserLoginLogEvent(
            data: ['foo' => 'bar'],
            piiData: ['bar' => 'baz'],
        );

        $service = new SysLogger(false, '', '', $mock);
        $service->log($event);
    }
}
