<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Rabbitmq;

use MinVWS\Logging\Laravel\Loggers\LogEventInterface;
use DateTimeInterface;
use Illuminate\Support\Arr;
use RabbitEvents\Publisher\Support\AbstractPublishableEvent;

class RabbitLogEvent extends AbstractPublishableEvent
{
    private LogEventInterface $event;

    public function __construct(LogEventInterface $event)
    {
        $this->event = $event;
    }

    public function publishEventKey(): string
    {
        return config('rabbitevents.prefix') . '.' . $this->getEventKey();
    }

    public function toPublish(): array
    {
        $logData = $this->event->getLogData();

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
        return config('rabbitevents.prefix');
    }

    /**
     * @psalm-suppress NoInterfaceProperties
     */
    private function getActorUserData(): array
    {
        $user = $this->event->getActor();

        return [
            'user_id' => $user?->id ?? '',
            'uzi_number' => $user?->uzi_number ?? '',
            'organisation_id' => $user?->organisation_id ?? '',
            'created_by' => $user?->created_by ?? '',
            'roles' => $user?->roles ?? [],
            'ip' => $this->getIpAddress(),
        ];
    }

    private function getIpAddress(): ?string
    {
        return Arr::get($this->event->getPiiLogData(), 'request.ip_address') ?? app('request')->ip() ?? null;
    }

    /**
     * Here you can define custom mappings for the object field based on the event.
     * For example: DeclarationLogEvent::class => $this->specificData(),
     * @return array
     */
    private function getObject(): array
    {
        return match ($this->event::class) {
            default => $this->getRequestFromLogData(),
        };
    }

    private function getRequestFromLogData(): array
    {
        $request = Arr::get($this->event->getLogData(), 'request', []);
        unset($request['source']);

        return $request;
    }
}
