<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

class ActivateAccountLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090004';
    public const EVENT_KEY = 'activate_account';

    public function __construct(
        public ?LoggableUser $user,
        public array $data = [],
        public array $piiData = [],
        public string $actionCode = '',
        public bool $allowedAdminView = false,
        public bool $failed = false,
        public string $source = '',
    ) {
        parent::__construct($user, $data, $piiData, self::EVENT_CODE, $actionCode, $allowedAdminView, $failed, $source);
    }
}
