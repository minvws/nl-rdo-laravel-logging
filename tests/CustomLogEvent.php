<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests;

use MinVWS\Logging\Laravel\Contracts\LoggableUser;
use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

class CustomLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '999999';
    public const EVENT_KEY = 'custom_test_event';

    public function __construct(
        public ?LoggableUser $actor = null,
        public ?LoggableUser $target = null,
        public array $data = [],
        public array $piiData = [],
        public string $actionCode = self::AC_CREATE,
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
