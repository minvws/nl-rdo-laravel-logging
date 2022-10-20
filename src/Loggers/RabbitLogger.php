<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Loggers;

use MinVWS\Logging\Laravel\Events\Logging\AccountChangeLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\ActivateAccountLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\AdminPasswordResetLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\DeclarationLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\RegistrationLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\ResetCredentialsLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\UserCreatedLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\UserLoginLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\UserLoginTwoFactorFailedEvent;
use MinVWS\Logging\Laravel\Events\Logging\UserLogoutLogEvent;
use MinVWS\Logging\Laravel\Events\Logging\VerificationCodeDisabledLogEvent;
use MinVWS\Logging\Laravel\Events\Rabbitmq\RabbitLogEvent;
use RabbitEvents\Publisher\Publisher;

class RabbitLogger implements LoggerInterface
{
    protected ?Publisher $publisher;
    private array $allowedEvents = [
        AccountChangeLogEvent::class,
        ActivateAccountLogEvent::class,
        AdminPasswordResetLogEvent::class,
        RegistrationLogEvent::class,
        DeclarationLogEvent::class,
        UserCreatedLogEvent::class,
        ResetCredentialsLogEvent::class,
        UserLoginLogEvent::class,
        UserLoginTwoFactorFailedEvent::class,
        UserLogoutLogEvent::class,
        VerificationCodeDisabledLogEvent::class,
    ];

    protected string $prefix;
    protected bool $logPii = false;

    public function __construct(
        array $additionalAllowedEvents = [],
        string $prefix = "",
        bool $logPii = false,
        ?Publisher $publisher = null
    ) {
        if (!function_exists('publish') && $publisher === null) {
            throw new \Exception("RabbitMQ publish() function is not found and no publisher has been given.");
        }

        $this->allowedEvents = array_merge($this->allowedEvents, $additionalAllowedEvents);
        $this->logPii = $logPii;
        $this->prefix = $prefix;
        $this->publisher = $publisher;
    }

    public function log(LogEventInterface $event): void
    {
        $rabbitLogEvent = new RabbitLogEvent($event, $this->prefix, $this->logPii);

        if ($this->publisher) {
            $this->publisher->publish($rabbitLogEvent);
        } else {
            publish($rabbitLogEvent);
        }
    }

    public function canHandleEvent(LogEventInterface $event): bool
    {
        return in_array($event::class, $this->allowedEvents);
    }
}
