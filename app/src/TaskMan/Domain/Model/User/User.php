<?php

declare(strict_types=1);

namespace App\TaskMan\Domain\Model\User;

use App\Shared\Domain\Exception\InvalidInputDataException;
use App\Shared\Domain\Model\Aggregate;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity()
 */
class User extends Aggregate implements UserInterface
{

    public const DEFAULT_USER_ROLE = 'ROLE_USER';

    public const MAX_USERNAME_LENGTH = 80;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=80, unique=true)
     */
    private string $username;

    /**
     * @var array<string>
     *
     * @ORM\Column(type="json", nullable=false)
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $password;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default"="CURRENT_TIMESTAMP"},
     *   nullable=false)
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @param string $username
     * @param string $password
     * @param UniqueUsernameSpecificationInterface $uniqueUsernameSpecification
     * @param array|string[] $roles
     */
    public function __construct(
      string $username,
      string $password,
      UniqueUsernameSpecificationInterface $uniqueUsernameSpecification,
      array $roles = [self::DEFAULT_USER_ROLE]
    ) {
        if (!$uniqueUsernameSpecification->isSatisfiedBy($username)) {
            throw new InvalidInputDataException(
              sprintf('Username %s already exists', $username)
            );
        }

        $this->setUsername($username);
        $this->setPassword($password);
        $this->setRoles($roles);
        $this->setCreatedAt(new \DateTimeImmutable());
    }

    public function eraseCredentials(): void
    {
    }

    public function equals(User $user): bool
    {
        return $this->getId() === $user->getId();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    private function setPassword(string $password): void
    {
        $this->password = $password;
    }

    private function setUsername(string $username): void
    {
        Assert::maxLength(
          $username,
          self::MAX_USERNAME_LENGTH,
          'Username should contain at most %2$s characters. Got: %s'
        );

        $this->username = $username;
    }

    private function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    private function setRoles(array $roles): void
    {
        if (!in_array(self::DEFAULT_USER_ROLE, $roles, true)) {
            $roles[] = self::DEFAULT_USER_ROLE;
        }

        $this->roles = array_unique($roles);
    }

}