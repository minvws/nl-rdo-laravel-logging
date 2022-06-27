<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

use MinVWS\Logging\Laravel\Contracts\LoggableUser;

class AccountChangeLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090001';
    public const EVENT_KEY = 'account_change';

    public const EVENTCODE_USERDATA = '900101';
    public const EVENTCODE_ROLES = '900102';
    public const EVENTCODE_TIMESLOT = '900103';
    public const EVENTCODE_ACTIVE = '900104';
    public const EVENTCODE_RESET = '900105';

    public const EVENTCODE_KVTB_USERDATA = '900201';
    public const EVENTCODE_KVTB_ROLES = '900202';
    public const EVENTCODE_KVTB_RESET = '900203';


    public function __construct(
        public ?LoggableUser $actor,
        public ?LoggableUser $target,
        public array $data = [],
        public array $piiData = [],
        public string $actionCode = '',
        public bool $allowedAdminView = false,
        public bool $failed = false,
        public string $source = '',
        public string $eventCode = self::EVENT_CODE,
    ) {
        parent::__construct(
            $actor,
            $target,
            $data,
            $piiData,
            $eventCode,
            $actionCode,
            $allowedAdminView,
            $failed,
            $source
        );
    }
}
