<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

use MinVWS\Logging\Laravel\Contracts\LoggableUser;

class AdminPasswordResetLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090005';
    public const EVENT_KEY = 'admin_password_reset';

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
