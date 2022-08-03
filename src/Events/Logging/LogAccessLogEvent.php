<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

use MinVWS\Logging\Laravel\Contracts\LoggableUser;

class LogAccessLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '080002';
    public const EVENT_KEY = 'log_access';

    public function __construct(
        public ?LoggableUser $actor,
        public ?LoggableUser $target = null,
        public array $data = [],
        public array $piiData = [],
        public string $actionCode = GeneralLogEvent::AC_READ,
        public bool $allowedAdminView = false,
        public bool $failed = false,
        public string $source = '',
    ) {
        parent::__construct(
            $actor,
            $target,
            $data,
            $piiData,
            self::EVENT_CODE,
            $actionCode,
            $allowedAdminView,
            $failed,
            $source
        );
    }
}
