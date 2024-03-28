<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests;

use MinVWS\Logging\Laravel\Contracts\LoggableUser;

class User implements LoggableUser
{
    public string $id;
    public string $email;
}
