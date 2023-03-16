<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

class DeclarationLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '080001';
    public const EVENT_KEY = 'declaration';
}
