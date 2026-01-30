<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests\Loggers;

use Illuminate\Log\LogManager;
use Illuminate\Log\Logger;
use MinVWS\AuditLogger\Events\Logging\UserLoginLogEvent;
use MinVWS\Logging\Laravel\LogService;
use MinVWS\Logging\Laravel\LogServiceProvider;
use MinVWS\Logging\Laravel\Tests\TestCase;
use MinVWS\Logging\Laravel\Tests\User;
use Mockery;

class SysLoggerProviderTest extends TestCase
{
    public function testRegistersSysloggerUsingChannel(): void
    {
        $psrMock = Mockery::mock(Logger::class);
        $psrMock->shouldReceive('info')->once()->withArgs(function ($args) {
            $this->assertStringStartsWith('AuditLog: ', $args);

            // Basic sanity: ensure payload decodes to JSON after base64
            $parts = explode(' ', $args, 2);
            $this->assertCount(2, $parts);
            $msg = base64_decode($parts[1], true);
            $this->assertNotFalse($msg);
            $data = json_decode($msg, true, 512, JSON_THROW_ON_ERROR);
            $this->assertIsArray($data);

            return true;
        });

        $logManagerMock = Mockery::mock(LogManager::class);
        $logManagerMock->shouldReceive('channel')->once()->with('my-channel')->andReturn($psrMock);

        // Bind the LogManager mock so the provider will call ->channel('my-channel')
        $this->app->instance(LogManager::class, $logManagerMock);

        // Configure the provider to enable syslog and use the channel
        $this->app['config']->set('logging.syslog_enabled', true);
        $this->app['config']->set('logging.syslog_channel', 'my-channel');
        $this->app['config']->set('logging.syslog_encrypt', false);
        $this->app['config']->set('logging.syslog_base64_encode_enabled', true);

        // Register the provider (boot will register the LogService singleton)
        $this->app->register(LogServiceProvider::class);

        $service = $this->app->make(LogService::class);

        $user = new User();
        $user->id = '12345';
        $user->email = 'john@example.org';

        $event = (new UserLoginLogEvent())->withActor($user)->withData(['foo' => 'bar'])->withPiiData(['bar' => 'baz']);

        $service->log($event);
    }

    public function testRegistersSysloggerUsingDefaultLog(): void
    {
        $psrMock = Mockery::mock(Logger::class);
        $psrMock->shouldReceive('info')->once()->withArgs(function ($args) {
            $this->assertStringStartsWith('AuditLog: ', $args);

            $parts = explode(' ', $args, 2);
            $this->assertCount(2, $parts);
            $msg = base64_decode($parts[1], true);
            $this->assertNotFalse($msg);
            $data = json_decode($msg, true, 512, JSON_THROW_ON_ERROR);
            $this->assertIsArray($data);

            return true;
        });

        // Bind the default 'log' entry in the container
        $this->app->instance('log', $psrMock);

        $this->app['config']->set('logging.syslog_enabled', true);
        $this->app['config']->set('logging.syslog_channel', null);
        $this->app['config']->set('logging.syslog_encrypt', false);
        $this->app['config']->set('logging.syslog_base64_encode_enabled', true);

        $this->app->register(LogServiceProvider::class);

        $service = $this->app->make(LogService::class);

        $event = (new UserLoginLogEvent())->withData(['foo' => 'bar']);

        $service->log($event);
    }
}
