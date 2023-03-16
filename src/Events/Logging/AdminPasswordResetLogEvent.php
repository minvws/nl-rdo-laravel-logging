<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

class AdminPasswordResetLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090005';
    public const EVENT_KEY = 'admin_password_reset';
}
