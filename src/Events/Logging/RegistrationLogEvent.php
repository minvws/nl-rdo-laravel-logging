<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

class RegistrationLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '080004';
    public const EVENT_KEY = 'registration';
}
