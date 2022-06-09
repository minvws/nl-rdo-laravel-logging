<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Loggers;

use MinVWS\Logging\Laravel\Events\Logging\AccountChangeLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\ActivateAccountLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\AdminPasswordResetLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\RegistrationLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\ResetCredentialsLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\UserCreatedLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\UserLoginLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\UserLogoutLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\VerificationCodeDisabledLogEvent;
use MinVWS\Logging\Laravel\Events\Rabbitmq\RabbitLogEvent;

class RabbitLogger implements LoggerInterface
{
    private array $allowedEvents = [
        AccountChangeLogEvent::class,
        ActivateAccountLogEvent::class,
        AdminPasswordResetLogEvent::class,
        RegistrationLogEvent::class,
        DeclarationLogEvent::class,
        UserCreatedLogEvent::class,
        ResetCredentialsLogEvent::class,
        UserLoginLogEvent::class,
        UserLogoutLogEvent::class,
        VerificationCodeDisabledLogEvent::class,
    ];

    public function __construct()
    {
        if (!function_exists('publish')) {
            throw new \Exception("RabbitMQ publish() function is not found.");
        }
    }

    public function log(LogEventInterface $event): void
    {
        $rabbitLogEvent = new RabbitLogEvent($event);

        publish($rabbitLogEvent);
    }

    public function canHandleEvent(LogEventInterface $event): bool
    {
        return in_array($event::class, $this->allowedEvents);
    }
}
