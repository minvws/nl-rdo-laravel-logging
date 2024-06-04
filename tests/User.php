<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests;

use MinVWS\AuditLogger\Contracts\LoggableUser;

class User implements LoggableUser
{
    public string $id;
    public string $email;
    public string $name;
    public array $roles = [];

    public function getAuditId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
