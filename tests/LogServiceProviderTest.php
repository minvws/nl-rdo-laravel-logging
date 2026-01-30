<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests;

use MinVWS\Logging\Laravel\LogService;
use MinVWS\Logging\Laravel\LogServiceProvider;
use Orchestra\Testbench\TestCase;

class LogServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [LogServiceProvider::class];
    }

    public function testSysloggerWithBase64EncodeEnabledTrue(): void
    {
        config(['logging.syslog_base64_encode_enabled' => true]);
        $logService = $this->app->make(LogService::class);
        $this->assertInstanceOf(LogService::class, $logService);
    }

    public function testSysloggerWithBase64EncodeEnabledFalse(): void
    {
        config(['logging.syslog_base64_encode_enabled' => false]);
        $logService = $this->app->make(LogService::class);
        $this->assertInstanceOf(LogService::class, $logService);
    }

    public function testSysloggerWithBase64EncodeEnabledDefaultValue(): void
    {
        $logService = $this->app->make(LogService::class);
        $this->assertInstanceOf(LogService::class, $logService);
    }
}
