<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests\Loggers;

use MinVWS\Logging\Laravel\Events\Logging\UserLoginLogEvent;
use MinVWS\Logging\Laravel\Loggers\RabbitLogger;
use MinVWS\Logging\Laravel\Tests\CustomLogEvent;
use MinVWS\Logging\Laravel\Tests\User;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RabbitEvents\Publisher\Publisher;
use RabbitEvents\Publisher\ShouldPublish;

class RabbitLoggerTest extends MockeryTestCase
{
    public function testRabbitLoggerWithPii(): void
    {
        $mock = Mockery::mock(Publisher::class);
        $mock->shouldReceive('publish')->once()->withArgs(function (ShouldPublish $event) {
            $this->assertEquals('phpunit.user_login', $event->publishEventKey());

            $data = $event->toPublish();
            $this->assertEquals('phpunit.user_login', $data['routing_key']);
            $this->assertEquals('phpunit', $data['source']);
            $this->assertEquals('12345', $data['user']['user_id']);

            $this->assertEquals(['foo' => 'bar', 'bar' => 'baz'], $data['object']);

            return true;
        });

        $user = new User();
        $user->email = "john@example.org";
        $user->id = '12345';

        $event = (new UserLoginLogEvent())
            ->withActor($user)
            ->withData(['foo' => 'bar'])
            ->withPiiData(['bar' => 'baz']);

        $service = new RabbitLogger([], 'phpunit', true, $mock);
        $service->log($event);
    }

    public function testRabbitLoggerWithoutPii(): void
    {
        $mock = Mockery::mock(Publisher::class);
        $mock->shouldReceive('publish')->once()->withArgs(function (ShouldPublish $event) {
            $this->assertEquals('phpunit.user_login', $event->publishEventKey());

            $data = $event->toPublish();
            $this->assertEquals('phpunit.user_login', $data['routing_key']);
            $this->assertEquals('phpunit', $data['source']);
            $this->assertEquals('12345', $data['user']['user_id']);

            $this->assertArrayNotHasKey('bar', $data['object']);
            $this->assertEquals(['foo' => 'bar'], $data['object']);

            return true;
        });

        $user = new User();
        $user->email = "john@example.org";
        $user->id = '12345';

        $event = (new UserLoginLogEvent())
            ->withActor($user)
            ->withData(['foo' => 'bar'])
            ->withPiiData(['bar' => 'baz']);

        $service = new RabbitLogger([], 'phpunit', false, $mock);
        $service->log($event);
    }

    public function testCanHandle(): void
    {
        $service = new RabbitLogger([], 'phpunit', true);
        $this->assertTrue($service->canHandleEvent(new UserLoginLogEvent()));
        $this->assertFalse($service->canHandleEvent(new CustomLogEvent()));

        $service = new RabbitLogger([CustomLogEvent::class], 'phpunit', true);
        $this->assertTrue($service->canHandleEvent(new UserLoginLogEvent()));
        $this->assertTrue($service->canHandleEvent(new CustomLogEvent()));
    }
}
