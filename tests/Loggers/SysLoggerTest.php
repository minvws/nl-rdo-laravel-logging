<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests\Loggers;

use Illuminate\Log\Logger;
use MinVWS\Logging\Laravel\Events\Logging\UserLoginLogEvent;
use MinVWS\Logging\Laravel\Loggers\SysLogger;
use MinVWS\Logging\Laravel\Tests\User;
use Mockery;

class SysLoggerTest extends Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testSysloggerWithoutEncryption(): void
    {
        $mock = Mockery::mock(Logger::class);
        $mock->shouldReceive('info')->once()->withArgs(function ($args) {

            $this->assertStringStartsWith('AuditLog: ', $args);

            $parts = explode(" ", $args, 2);
            $this->assertCount(2, $parts);

            $msg = base64_decode($parts[1], true);
            $this->assertNotFalse($msg);

            $data = json_decode($msg, true, 512, JSON_THROW_ON_ERROR);
            $this->assertIsArray($data);

            $this->assertEquals(UserLoginLogEvent::EVENT_CODE, $data['event_code']);
            $this->assertArrayHasKey('foo', $data['context']);
            $this->assertArrayHasKey('bar', $data['context']);

            $this->assertEquals('12345', $data['user_id']);
            $this->assertEquals('john@example.org', $data['email']);

            // Should return a json base64 encoded string
            return true;
        });

        $user = new User();
        $user->email = "john@example.org";
        $user->id = '12345';

        $event = (new UserLoginLogEvent())
            ->withActor($user)
            ->withData(['foo' => 'bar'])
            ->withPiiData(['bar' => 'baz']);

        $service = new SysLogger(false, '', '', $mock);
        $service->log($event);
    }

    public function testSysloggerWithEncryption(): void
    {
        if (! function_exists('sodium_crypto_box_keypair')) {
            $this->markTestSkipped('No sodium detected');
        }

        $keyPair1 = sodium_crypto_box_keypair();
        $publicKey1 = sodium_crypto_box_publickey($keyPair1);
        $privateKey1 = sodium_crypto_box_secretkey($keyPair1);

        $keyPair2 = sodium_crypto_box_keypair();
        $publicKey2 = sodium_crypto_box_publickey($keyPair2);
        $privateKey2 = sodium_crypto_box_secretkey($keyPair2);

        $mock = Mockery::mock(Logger::class);
        $mock->shouldReceive('info')->once()->withArgs(function ($args) use ($privateKey2, $publicKey1) {
            $this->assertStringStartsWith('AuditLog: ', $args);

            $parts = explode(" ", $args, 2);
            $this->assertCount(2, $parts);

            $box = base64_decode($parts[1]);
            $nonce = substr($box, 0, SODIUM_CRYPTO_BOX_NONCEBYTES);
            $cipher = substr($box, SODIUM_CRYPTO_BOX_NONCEBYTES);

            $pair = sodium_crypto_box_keypair_from_secretkey_and_publickey($privateKey2, $publicKey1);
            $msg = sodium_crypto_box_open($cipher, $nonce, $pair);

            $data = json_decode($msg, true, 512, JSON_THROW_ON_ERROR);
            $this->assertIsArray($data);

            $this->assertEquals(UserLoginLogEvent::EVENT_CODE, $data['event_code']);
            $this->assertArrayHasKey('foo', $data['context']);
            $this->assertArrayHasKey('bar', $data['context']);

            $this->assertEquals('12345', $data['user_id']);
            $this->assertEquals('john@example.org', $data['email']);

            // Should return a json base64 encoded string
            return true;
        });

        $user = new User();
        $user->email = "john@example.org";
        $user->id = '12345';

        $event = (new UserLoginLogEvent())
            ->withActor($user)
            ->withData(['foo' => 'bar'])
            ->withPiiData(['bar' => 'baz']);

        $service = new SysLogger(true, $publicKey2, $privateKey1, $mock);
        $service->log($event);
    }
}
