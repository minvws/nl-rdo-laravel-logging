<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;

class CustomLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '999999';
    public const EVENT_KEY = 'custom_test_event';
}
