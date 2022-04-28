<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

use MinVWS\Logging\Laravel\Contracts\LoggableUser;

class AmpUploadEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090006';
    public const EVENT_KEY = 'amp_upload';

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
