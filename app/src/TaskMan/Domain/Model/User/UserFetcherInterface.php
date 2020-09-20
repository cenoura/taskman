<?php

declare(strict_types=1);

namespace App\TaskMan\Domain\Model\User;

interface UserFetcherInterface
{

    public function fetchAuthenticatedUser(): User;

}