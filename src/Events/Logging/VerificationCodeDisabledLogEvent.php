<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

use MinVWS\Logging\Laravel\Contracts\LoggableUser;

class VerificationCodeDisabledLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '080003';
    public const EVENT_KEY = 'verification_code_disabled';

    public function __construct(
        public ?LoggableUser $actor,
        public ?LoggableUser $target,

        public array $data = [],
        public array $piiData = [],
        public string $actionCode = '',
        public bool $allowedAdminView = false,
        public bool $failed = false,
        public string $source = '',
    ) {
        parent::__construct($actor, $target, $data, $piiData, self::EVENT_CODE, $actionCode, $allowedAdminView, $failed, $source);
    }
}
