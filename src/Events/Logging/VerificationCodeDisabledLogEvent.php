<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

class VerificationCodeDisabledLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '080003';
    public const EVENT_KEY = 'verification_code_disabled';
}
