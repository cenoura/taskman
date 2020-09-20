<?php

declare(strict_types=1);

namespace App\Tests\Unit\TaskMan\Domain\Model\User;

use App\TaskMan\Domain\Model\User\UniqueUsernameSpecificationInterface;
use App\TaskMan\Domain\Model\User\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{

    public function testItThrowsExceptionWhenUsernameTooLong(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches(
          '/Username should contain at most/'
        );

        new User(
          str_repeat('a', User::MAX_USERNAME_LENGTH + 1),
          'password',
          $this->getUniqueUsernameSpecification()
        );
    }

    public function testItSetsDefaultRoleUser(): void
    {
        $user = new User(
          'user1',
          'password1',
          $this->getUniqueUsernameSpecification()
        );

        self::assertContains(User::DEFAULT_USER_ROLE, $user->getRoles());

        $user = new User(
          'user2',
          'password2',
          $this->getUniqueUsernameSpecification(),
          ['ROLE_ADMIN']
        );

        self::assertContains(User::DEFAULT_USER_ROLE, $user->getRoles());
    }

    public function testItPassesWhenValidValuesAreSet(): void
    {
        $user = new User(
          "username1",
          "password1",
          $this->getUniqueUsernameSpecification()
        );

        self::assertInstanceOf(User::class, $user);
    }

    public function testItReturnFalseWhenUsersAreNotEquals(): void
    {
        $user1 = new User(
          'user1',
          'password1',
          $this->getUniqueUsernameSpecification()
        );

        $user2 = new User(
          'user2',
          'password2',
          $this->getUniqueUsernameSpecification()
        );

        $user3 = new User(
          'user1',
          'password1',
          $this->getUniqueUsernameSpecification()
        );

        $this->setUserId($user1, 1);
        $this->setUserId($user2, 2);
        $this->setUserId($user3, 1);

        self::assertFalse($user1->equals($user2));
        self::assertTrue($user1->equals($user3));
    }

    private function getUniqueUsernameSpecification(
    ): UniqueUsernameSpecificationInterface
    {
        $specification = $this->createMock(
          UniqueUsernameSpecificationInterface::class
        );
        $specification->method('isSatisfiedBy')->willReturn(true);

        return $specification;
    }

    private function setUserId(User $user, int $id): void
    {
        $userReflection = new \ReflectionClass($user);
        $idProperty = $userReflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($user, $id);
    }

}