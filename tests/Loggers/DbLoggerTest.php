<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests\Loggers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use MinVWS\Logging\Laravel\Events\Logging\UserLoginLogEvent;
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

        $event = (new UserLoginLogEvent())
            ->withActor($user)
            ->withData(['foo' => 'bar'])
            ->withPiiData(['bar' => 'baz']);

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
                'context' => ['foo' => 'bar'],
                'pii_context' => base64_encode(json_encode([
                    "context" => ['bar' => 'baz'],
                    "email" => null
                ])),
                'created_at' => $actual['created_at'],
                'event_code' => '091111',
                'action_code' => 'E',
                'allowed_admin_view' => false,
                'failed' => false,
            ])->toArray(),
            $actual->toArray()
        );
    }
}
