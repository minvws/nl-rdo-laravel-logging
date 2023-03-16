<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

class UserLogoutLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '092222';
    public const EVENT_KEY = 'user_logout';
}
