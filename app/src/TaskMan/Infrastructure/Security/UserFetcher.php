<?php

declare(strict_types=1);

namespace App\TaskMan\Infrastructure\Security;

use App\TaskMan\Domain\Model\User\User;
use App\TaskMan\Domain\Model\User\UserFetcherInterface;
use Symfony\Component\Security\Core\Security;

final class UserFetcher implements UserFetcherInterface
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function fetchAuthenticatedUser(): User
    {
        $user = $this->security->getUser();

        if ($user === null) {
            throw new \InvalidArgumentException('Current user not found.');
        }

        if (!($user instanceof User)) {
            throw new \InvalidArgumentException(sprintf('Invalid user.'));
        }

        return $user;
    }

}