<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Loggers;

use MinVWS\AuditLogger\Events\Logging\AccountChangeLogEvent;
use MinVWS\AuditLogger\Events\Logging\ActivateAccountLogEvent;
use MinVWS\AuditLogger\Events\Logging\AdminPasswordResetLogEvent;
use MinVWS\AuditLogger\Events\Logging\DeclarationLogEvent;
use MinVWS\AuditLogger\Events\Logging\RegistrationLogEvent;
use MinVWS\AuditLogger\Events\Logging\ResetCredentialsLogEvent;
use MinVWS\AuditLogger\Events\Logging\UserCreatedLogEvent;
use MinVWS\AuditLogger\Events\Logging\UserLoginLogEvent;
use MinVWS\AuditLogger\Events\Logging\UserLoginTwoFactorFailedEvent;
use MinVWS\AuditLogger\Events\Logging\UserLogoutLogEvent;
use MinVWS\AuditLogger\Events\Logging\VerificationCodeDisabledLogEvent;
use MinVWS\AuditLogger\Loggers\LogEventInterface;
use MinVWS\AuditLogger\Loggers\LoggerInterface;
use MinVWS\Logging\Laravel\Events\Rabbitmq\RabbitLogEvent;
use RabbitEvents\Publisher\Publisher;

class RabbitLogger implements LoggerInterface
{
    protected ?Publisher $publisher;

    /**
     * @var string[]
     */
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

    /**
     * @param string[] $additionalAllowedEvents
     */
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

    #[\Override]
    public function log(LogEventInterface $event): void
    {
        $rabbitLogEvent = new RabbitLogEvent($event, $this->prefix, $this->logPii);

        if ($this->publisher) {
            $this->publisher->publish($rabbitLogEvent);
        } else {
            publish($rabbitLogEvent);
        }
    }

    #[\Override]
    public function canHandleEvent(LogEventInterface $event): bool
    {
        return in_array($event::class, $this->allowedEvents);
    }
}
