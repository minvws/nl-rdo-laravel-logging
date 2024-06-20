<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests\Loggers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use MinVWS\AuditLogger\Events\Logging\UserLoginLogEvent;
use MinVWS\Logging\Laravel\Loggers\DbLogger;
use MinVWS\Logging\Laravel\Loggers\ModelFactoryInterface;
use MinVWS\Logging\Laravel\Models\AuditLog;
use MinVWS\Logging\Laravel\Tests\TestCase;
use Mockery;
use MinVWS\Logging\Laravel\Tests\User;

class DbLoggerTest extends TestCase
{
    public function testDblogger(): void
    {
        $user = new User();
        $user->id = '12345';
        $user->email = 'john.doe@example.org';

        $event = (new UserLoginLogEvent())
            ->withActor($user)
            ->withData(['foo' => 'bar'])
            ->withPiiData(['bar' => 'baz'])
            ->withSource('my-source');

        $service = new DbLogger(
            false,
            '',
            '',
            AuditLog::class,
        );

        $service->log($event);

        $actualAll = AuditLog::all();
        $this->assertCount(1, $actualAll);
        $actual = $actualAll->first();

        $this->assertEquals(
            AuditLog::create([
                'email' => null,
                'request' => ['foo' => 'bar'],
                'pii_request' => base64_encode(json_encode([
                    "request" => ['bar' => 'baz'],
                    "email" => 'john.doe@example.org'
                ])),
                'created_at' => $actual['created_at'],
                'event_code' => '091111',
                'action_code' => 'E',
                'source' => 'my-source',
                'allowed_admin_view' => false,
                'failed' => false,
                'failed_reason' => null,
            ])->toArray(),
            $actual->toArray()
        );
    }
}
