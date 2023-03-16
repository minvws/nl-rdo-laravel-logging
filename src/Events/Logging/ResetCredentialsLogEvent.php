<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

use MinVWS\Logging\Laravel\Contracts\LoggableUser;

class ResetCredentialsLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090003';
    public const EVENT_KEY = 'reset_credentials';
}
