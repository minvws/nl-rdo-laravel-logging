<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Rabbitmq;

use DateTimeInterface;
use Illuminate\Support\Arr;
use MinVWS\AuditLogger\Loggers\LogEventInterface;
use RabbitEvents\Publisher\Support\AbstractPublishableEvent;

/**
 * @phpstan-type ActorData array{
 *     user_id: string,
 *     name: string,
 *     roles: string[],
 *     ip: string|null
 * }
 */
class RabbitLogEvent extends AbstractPublishableEvent
{
    private LogEventInterface $event;
    private string $prefix;
    private bool $logPii;

    public function __construct(LogEventInterface $event, string $prefix, bool $logPii = false)
    {
        $this->event = $event;
        $this->logPii = $logPii;
        $this->prefix = $prefix;
    }

    #[Override]
    public function publishEventKey(): string
    {
        return $this->prefix . '.' . $this->getEventKey();
    }

    #[Override]
    public function toPublish(): array
    {
        $logData = $this->logPii ? $this->event->getMergedPiiData() : $this->event->getLogData();

        $publish = [
            'routing_key' => $this->publishEventKey(),
            'event_code' => Arr::get($logData, 'event_code'),
            'action_code' => Arr::get($logData, 'action_code'),
            'timestamp' => $this->getTimestamp(),
            'result' => $this->getResult(),
            'source' => $this->getSource(),
            'user' => $this->getActorUserData(),
            'object' => $this->getObject(),
        ];

        $failedReason = Arr::get($logData, 'failed_reason');
        if (!empty($failedReason)) {
            $publish['failed_reason'] = $failedReason;
        }

        return $publish;
    }

    private function getEventKey(): string
    {
        return $this->event->getEventKey();
    }

    private function getTimestamp(): int
    {
        $createdAt = Arr::get($this->event->getLogData(), 'created_at');
        if ($createdAt instanceof DateTimeInterface) {
            return $createdAt->getTimestamp();
        }

        return time();
    }

    private function getResult(): int
    {
        return Arr::get($this->event->getLogData(), 'failed', true) === false ? 1 : 0;
    }

    private function getSource(): string
    {
        return $this->prefix;
    }

    /**
     * @phpstan-return ActorData
     */
    private function getActorUserData(): array
    {
        $user = $this->event->getActor();

        return [
            'user_id' => $user?->getAuditId() ?? '',
            'name' => $user?->getName() ?? '',
            'roles' => $user?->getRoles() ?? [],
            'ip' => $this->getIpAddress(),
        ];
    }

    private function getIpAddress(): ?string
    {
        $ip = Arr::get($this->event->getPiiLogData(), 'request.ip_address');

        if ($ip !== null) {
            return $ip;
        }

        // We need to explicitly check for request service because unittests do not have these :/
        return app()->has('request') ? app('request')->ip() : null;
    }

    /**
     * Here you can define custom mappings for the object field based on the event.
     * For example: DeclarationLogEvent::class => $this->specificData(),
     * @return array<array-key,mixed>
     */
    private function getObject(): array
    {
        return match ($this->event::class) {
            default => $this->getRequestFromLogData(),
        };
    }

    /**
     * @return array<array-key,mixed>
     */
    private function getRequestFromLogData(): array
    {
        $data = $this->logPii ? $this->event->getMergedPiiData() : $this->event->getLogData();

        $context = Arr::get($data, 'request', []);
        unset($context['source']);

        return $context;
    }
}
