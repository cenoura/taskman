<?php

declare(strict_types=1);

namespace App\TaskMan\Application\Command\Authentication\CreateAuthToken;

final class CreateAuthTokenCommand
{

    private string $username;

    private string $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

}