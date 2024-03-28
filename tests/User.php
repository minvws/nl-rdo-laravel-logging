<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests;

use MinVWS\Logging\Laravel\Contracts\LoggableUser;

class User implements LoggableUser
{
    public string|null $id = null;
    public string|null $email = null;
    public string|null $name = null;
    public string|null $ggd_region = null;
    public string|null $roles = null;
}
