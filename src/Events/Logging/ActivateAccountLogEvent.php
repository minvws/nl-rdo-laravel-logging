<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

class ActivateAccountLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090004';
    public const EVENT_KEY = 'activate_account';
}
