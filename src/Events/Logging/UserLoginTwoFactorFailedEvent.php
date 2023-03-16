<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

class UserLoginTwoFactorFailedEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '093333';
    public const EVENT_KEY = 'user_login_two_factor_failed';
}
