<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

use Illuminate\Http\Request;
use MinVWS\Logging\Laravel\Contracts\LoggableUser;
use MinVWS\Logging\Laravel\Loggers\LogEventInterface;

abstract class GeneralLogEvent implements LogEventInterface
{
    public const AC_CREATE = 'C';
    public const AC_READ = 'R';
    public const AC_UPDATE = 'U';
    public const AC_DELETE = 'D';
    public const AC_EXECUTE = 'E';

    public const SOURCE_KVTB = 'kvtb';
    public const SOURCE_INGE3 = 'inge3';

    public const EVENT_KEY = 'log';

    public function __construct(
        public ?LoggableUser $actor,
        public ?LoggableUser $target,

        public array $data = [],
        public array $piiData = [],
        public string $eventCode = '',
        public string $actionCode = '',
        public bool $allowedAdminView = false,
        public bool $failed = false,
        public string $source = '',
    ) {
        if (!empty($this->source)) {
            $this->data['source'] = $this->source;
        }
    }

    public function getLogData(): array
    {
        return [
            'user_id' => $this->actor?->id,
            'target_user_id' => $this->target?->id,
            'request' => $this->data,
            'created_at' => now(),
            'event_code' => $this->eventCode,
            'action_code' => $this->actionCode[0],
            'allowed_admin_view' => $this->allowedAdminView,
            'failed' => $this->failed,
        ];
    }

    public function getPiiLogData(): array
    {
        $data = $this->piiData;

        if (config('logging.log_full_request', false)) {
            $httpRequest = Request::capture();

            $data['http_request'] = $httpRequest->request->all();
            $data['name'] = $this->actor?->name;
            $data['region_code'] = $this->actor?->ggd_region;
            $data['roles'] = $this->actor?->roles;
        }

        return [
            'request' => $data,
            'email' => $this->actor?->email,
        ];
    }

    public function getMergedPiiData(): array
    {
        $result = array_merge($this->getLogData(), $this->getPiiLogData());

        $request = $this->getLogData()['request'];
        foreach ($this->getPiiLogData()['request'] as $field => $value) {
            if (isset($request[$field])) {
                $request['pii_' . $field] = $value;
            } else {
                $request[$field] = $value;
            }
        }

        $result['request'] = $request;
        return $result;
    }

    public function getEventKey(): string
    {
        return static::EVENT_KEY;
    }

    public function getActor(): ?LoggableUser
    {
        return $this->actor;
    }

    public function getTargetUser(): ?LoggableUser
    {
        return $this->target;
    }
}
