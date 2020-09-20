<?php

declare(strict_types=1);

namespace App\TaskMan\Domain\Model\User;

interface UniqueUsernameSpecificationInterface
{

    public function isSatisfiedBy(string $username): bool;

}