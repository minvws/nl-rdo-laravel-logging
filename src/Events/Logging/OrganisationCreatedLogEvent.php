<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Logging;

class OrganisationCreatedLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090012';
    public const EVENT_KEY = 'organisation_created';
}
